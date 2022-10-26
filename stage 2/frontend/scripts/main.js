const $modal = document.querySelector('#modal')

const handlerShowModal = (e) => {
    $modal.classList.add('show')
}

const handlerCloseModal = (e) => {
    $modal.classList.remove('show')
}