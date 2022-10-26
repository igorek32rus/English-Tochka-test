class Modal {
    #modalHTML = null       // корень модалки

    constructor(idModal = "#modal") {
        this.#modalHTML = document.querySelector(idModal)
        this.handlerClose = this.handlerClose.bind(this)    // обработчик закрытия

        const html = `
            <div class="modal__backdrop"></div>
            <div class="modal__window">
                <div class="modal__close">&times;</div>
                <div class="modal__header"></div>
                <div class="modal__content"></div>
            </div>
        `

        this.#modalHTML.insertAdjacentHTML('beforeend', html)

        const $modalBackdrop = this.#modalHTML.querySelector('.modal__backdrop')
        const $modalClose = this.#modalHTML.querySelector('.modal__close')

        $modalBackdrop.addEventListener('click', this.handlerClose)     // по клику на бэкдроп
        $modalClose.addEventListener('click', this.handlerClose)        // или крестик - закрыть
    }

    handlerClose() {
        this.#modalHTML.classList.remove('show')
    }

    showModal(title, content) {     // показать модалку
        const $modalHeader = this.#modalHTML.querySelector('.modal__header')
        const $modalContent = this.#modalHTML.querySelector('.modal__content')

        $modalHeader.innerHTML = title
        $modalContent.innerHTML = content
        
        this.#modalHTML.classList.add('show')
    }
}

export default Modal;