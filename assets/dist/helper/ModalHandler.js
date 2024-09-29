// assets/javascript/controllers/modalHandler.js
export default class ModalHandler {
    constructor(swal) {
        this.swal = swal;
    }
    
    showModal(options) {
        this.swal.fire({
            ...options,
            allowOutsideClick: false
        });
    }
    
    showLoading(text) {
        this.swal.update({
            text: text,
            icon: 'question'
        });
        this.swal.showLoading();
    }
    
    handleError(error) {
        this.swal.update({
            text: 'Request failed !!',
            icon: 'warning'
        });
        this.swal.showValidationMessage(`${error}`);
    }
}
