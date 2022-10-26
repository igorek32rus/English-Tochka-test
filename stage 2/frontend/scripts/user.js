import Balance from "./balance"
import Products from "./products"
import { URLS } from "./config"

class User {
    #authHTML = null        // корень авторизации
    balance = new Balance()
    buyProduct = this.buyProduct.bind(this)
    products = new Products(".products .products__block", this.buyProduct)

    constructor(idAuth = "#authorization") {
        this.#authHTML = document.querySelector(idAuth)     // запоминаем корень авторизации
        this.handlerAuthBtn = this.handlerAuthBtn.bind(this)    // обработчик кнопки авторизации

        const $btnAuth = this.#authHTML.querySelector('button')
        $btnAuth.addEventListener('click', this.handlerAuthBtn)
    }

    async buyProduct(idProduct) {   // покупка продукта
        if (this.boughtProducts.includes(idProduct.toString())) {       // проверка, был ли куплен ранее
            return window.modal.showModal("Ошибка", "Продукт уже был приобретён")
        }

        const login = this.login
        const data = {
            product_id: idProduct,
            login
        }
    
        try {
            const response = await fetch(URLS.BUY_PRODUCT, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
    
            const json = await response.json()
    
            if (json.error) {
                return window.modal.showModal("Ошибка", json.error)
            }
    
            // обновление
            this.boughtProducts = json.products
            this.products.update(json.products)
            this.balance.update(json.balance)
        } catch (error) {
            console.log(error)
        }
    }

    async handlerAuthBtn() {
        const $loginField = this.#authHTML.querySelector('input[type=text]')
        const $messageBox = this.#authHTML.parentElement.querySelector('.message__auth')

        if (!$loginField.value.trim()) {
            return window.modal.showModal("Ошибка", "Имя пользователя не может быть пустым")
        }

        const data = {
            login: $loginField.value
        }
    
        try {
            const response = await fetch(URLS.AUTH_USER, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
    
            const json = await response.json()
    
            if (json.error) {       // если ошибка авторизации, обнулить баланс, отключить кнопки и вывести сообщение
                this.balance.update(0)
                this.products.disable()
                return $messageBox.innerText = json.error
            }

            // запись данных пользователя
            this.id = json.id
            this.login = json.login
            this.name = json.name
            this.boughtProducts = json.products
            // обновление данных баланса и продуктов
            this.balance.update(json.balance)
            this.products.enable()
            this.products.update(json.products)
    
            $messageBox.textContent = `Здравствуйте, ${json.name}`
        } catch (error) {
            console.log(error)
        }
    }
}

export default User;