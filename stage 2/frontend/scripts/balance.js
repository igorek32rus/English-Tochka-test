class Balance {
    #balanceHTML = null     // корневой HTML элемент баланса

    constructor(idBalance = ".wrapper .balance") {
        this.#balanceHTML = document.querySelector(idBalance)        // установка корня баланса
    }

    update(money) {     // обновление баланса
        this.balance = money
        const $balanceMoney = this.#balanceHTML.querySelector('.balance__money')
        $balanceMoney.textContent = this.balance
    }
}

export default Balance;