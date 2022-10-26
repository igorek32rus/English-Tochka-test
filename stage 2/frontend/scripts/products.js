import { parsePercent } from "./utils"
import { URLS } from "./config"

class Products {
    #productsHTML = null     // корневой HTML элемент продуктов
    products = []            // массив всех продуктов полученный с api
    disabled = true          // отключить кнопки покупки продуктов?

    constructor(idProducts = ".products .products__block", buyProduct) {
        this.#productsHTML = document.querySelector(idProducts)     // установка корня для продуктов
        this.handlerBuyProduct = this.handlerBuyProduct.bind(this)  // обработчик кнопки покупки
        this.buyProduct = buyProduct        // запоминаем метод для передачи в user
        this.getProducts()
    }

    async getProducts() {   // получение массива всех продуктов
        try {
            const response = await fetch(URLS.GET_ALL_PRODUCTS)
            const json = await response.json()
    
            if (json.error) {
                return window.modal.showModal("Ошибка", json.error)
            }
    
            this.products = json.products
            this.showProducts()
        } catch (error) {
            console.log(error)
        }
    }

    showProducts() {        // вывод всех продуктов
        this.products.forEach((product, i) => {

            // пришлось захардкодить иконки продуктов
            // в идеале для каждого продукта хранить имя иконки в бд
            let icon = ""
            switch (parseInt(product.id)) {
                case 1:
                    icon = "phone"
                    break;
                case 2:
                    icon = "opened_book"
                    break;
                default:
                    icon = "books"
                    break;
            }

            const percentStr = parsePercent(product.description)    // делим описание на ["10%", "за курс"]

            const html = `
                <div class="product__item" data-id="${product.id}">
                    <div class="money product__price">${product.price}</div>
                    <div class="product__icon ${icon}">
                        ${i === 0 ? `<div class="count__calls">x2</div>` : ''}
                    </div>
                    <div class="product__about">
                        ${percentStr[0] ? `<div class="product__percent">${percentStr[0]}</div>` : ''}
                        <div class="product__text">${percentStr[1]}</div>
                    </div>
                    <button class="button product__button disabled">Использовать скидку</button>
                </div>
            `

            this.#productsHTML.insertAdjacentHTML('beforeend', html)
        })

        const $btns = this.#productsHTML.querySelectorAll('.product__item .product__button')
        $btns.forEach(btn => {
            btn.addEventListener('click', this.handlerBuyProduct)       // обработчик кнопки покупки
        })
    }

    handlerBuyProduct(e) {
        if (this.disabled) return       // если кнопки выключены - игнор

        const idProduct = e.target.parentNode.dataset.id
        this.buyProduct(idProduct)      // передача методу класса User для покупки
    }

    disable() {     // отключить кнопки покупки
        this.disabled = true
        this.products.forEach(product => {
            const $productItem = this.#productsHTML.querySelector(`.product__item[data-id="${product.id}"]`)
            const $productBtn = $productItem.querySelector('button')

            $productBtn.classList.remove("product__used")
            $productBtn.innerText = "Использовать скидку"
            $productBtn.classList.add("disabled")
        })
    }

    enable() {      // включить кнопки покупки
        this.disabled = false
        this.products.forEach(product => {
            const $productItem = this.#productsHTML.querySelector(`.product__item[data-id="${product.id}"]`)
            const $productBtn = $productItem.querySelector('button')

            $productBtn.classList.remove("product__used")
            $productBtn.innerText = "Использовать скидку"
            $productBtn.classList.remove("disabled")
        })
    }

    update(userProducts) {      // обновление кнопок при авторизации или покупке
        this.products.forEach(product => {
            const $productItem = this.#productsHTML.querySelector(`.product__item[data-id="${product.id}"]`)
            const $productBtn = $productItem.querySelector('button')

            if (userProducts.includes(product.id)) {
                $productBtn.classList.add("product__used")
                $productBtn.innerText = "Уже использовано"
                return
            }
            
            $productBtn.classList.remove("product__used")
            $productBtn.innerText = "Использовать скидку"
        })
    }
}

export default Products;