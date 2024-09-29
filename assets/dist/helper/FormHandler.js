// assets/javascript/controllers/formHandler.js
export default class FormHandler {
    constructor(element, swal) {
        this.element = element;
        this.swal = swal;
    }
    
    validateForm() {
        const form = this.element.querySelector('form');
        const inputFields = [...form.querySelectorAll('input, textarea, select')];
        
        const formIsValid = inputFields.every(field =>
            !field.hasAttribute('required') || field.value.trim()
        );
        
        inputFields.forEach(field => {
            field.classList.toggle("is-invalid", field.hasAttribute('required') && !field.value.trim());
        });
        
        return formIsValid;
    }
    
    async submitForm(signal) {
        const form = this.element.querySelector('form');
        const formData = new FormData(form);
        
        if (!this.validateForm()) return;
        
        const response = await fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal,
            credentials: 'include'
        });
        
        return this.handleResponse(response);
    }
    
    async handleResponse(response) {
        if (!response.ok) {
            throw new Error(`Network error: ${response.status} - ${response.statusText}`);
        }
        return response.text();
    }
}
