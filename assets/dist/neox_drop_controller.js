import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dropzone'];
    
    connect() {
        this.isProcessing = false; // Définir isProcessing dans connect()
        this.addEventListeners();
    }
    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side-effects
        const { dropzoneTarget } = this;
        dropzoneTarget.removeEventListener('dragover', this.handleDragOver.bind(this));
        dropzoneTarget.removeEventListener('dragleave', this.handleMouseOut.bind(this));
        dropzoneTarget.removeEventListener('drop', this.handleDrop.bind(this));
    }
    
    addEventListeners() {
        const { dropzoneTarget } = this;
        dropzoneTarget.addEventListener('dragover', this.handleDragOver.bind(this));
        dropzoneTarget.addEventListener('dragleave', this.handleMouseOut.bind(this));
        dropzoneTarget.addEventListener('drop', this.handleDrop.bind(this));
    }

    
    handleDragOver(event) {
        event.preventDefault();
        this.dropzoneTarget.classList.add('dropzone-hover');
    }
    
    handleMouseOut() {
        this.dropzoneTarget.classList.remove('dropzone-hover');
    }
    
    handleDrop(event) {
        event.preventDefault();
        const item = event.dataTransfer.items[0];
        this.processDrop(item);
        this.handleMouseOut();
    }
    
    // processDrop(item) {
    //     if (item?.kind === 'string') {
    //         item.getAsString((plainText) => {
    //             const url = plainText.trim().split('\n')[0];
    //             console.log('Dropped URL:', url);
    //             this.validateAndProcessURL(url);
    //         });
    //     }
    // }
    
    processDrop(item) {
        if (item?.kind === 'string') {
            item.getAsString((plainText) => {
                const text = plainText.trim().split('\n')[0];
                console.log('Dropped Text:', text);
                
                if (item.type === 'text/x-moz-place') {
                    // Process bookmark URL from Firefox
                    const bookmarkData = JSON.parse(text); // Firefox fournit des données JSON
                    const bookmarkURL = bookmarkData.uri; // Récupérer l'URL du bookmark
                    console.log('Dropped Bookmark URL:', bookmarkURL);
                    this.validateAndProcessURL(bookmarkURL); // Valider et traiter l'URL du bookmark
                } else {
                    // Vérifier si c'est une URL
                    const urlPattern = new RegExp('^(https?:\\/\\/)?'+
                        '((([a-zA-Z0-9\\-]+\\.)+[a-zA-Z]{2,})|'+
                        'localhost|' +
                        '\\d{1,3}(\\.\\d{1,3}){3})' +
                        '(\\:\\d+)?(\\/[-a-zA-Z0-9@:%._\\+~#=]*)*'+
                        '(\\?[;&a-zA-Z0-9%_.~+=-]*)?'+
                        '(\\#[-a-zA-Z0-9_]*)?$', 'i');
                    
                    if (urlPattern.test(text)) {
                        console.log('Valid URL:', text);
                        this.validateAndProcessURL(text);
                    } else {
                        console.log('Not a URL, handling as plain text:', text);
                        this.processPlainText(text);
                    }
                }
            });
        }
    }
    
    validateAndProcessURL(url) {
        if (this.isValidURL(url)) {
            this.processURL(url);
        } else {
            this.handleInvalidURL(url);
        }
    }
    
    handleInvalidURL(url) {
        console.warn('Invalid URL dropped:', url);
        this.updateDropzoneStyle(false);
        this.showError(`Invalide : ${url}`);
    }
    
    updateDropzoneStyle(isValid) {
        const { dropzoneTarget } = this;
        dropzoneTarget.classList.toggle('dropzone-valid', isValid);
        dropzoneTarget.classList.toggle('dropzone-invalid', !isValid);
        
        // Restore the initial style after 3 seconds
        setTimeout(() => {
            dropzoneTarget.classList.remove('dropzone-valid', 'dropzone-invalid');
        }, 3000);
    }
    
    showError(message) {
        console.error(message);
        const initialContent = this.dropzoneTarget.innerHTML; // Use innerHTML to persist HTML tags
        
        // Add an error icon here, for example a cross icon
        const errorIcon = '<twig:ux:icon name="fa6-solid:exclamation-circle" width="20" height="20" color="red" class="mx-2" />';
        
        // Update content with error message and icon
        this.dropzoneTarget.innerHTML = `${errorIcon} ${message}`;
        this.dropzoneTarget.style.color = 'red'; // Change text color to indicate error
        
        // Restore content and color after 5 seconds
        setTimeout(() => {
            this.dropzoneTarget.innerHTML = initialContent; // Restore original content
            this.dropzoneTarget.style.color = ''; // Reset the initial color
        }, 5000);
    }
    
    processURL(url) {
        this.updateURLField(url);
        this.updateDropzoneStyle(true);
        const modalTriggerElement = this.element.closest('[data-action="click->xorgxx--neox-dashboard-bundle--neox-modal#modal"]');
        if (modalTriggerElement) {
            modalTriggerElement.setAttribute('data-domain', url); // Update URL in a data-url attribute
            modalTriggerElement.click(); // Triggers the click event
            modalTriggerElement.removeAttribute('data-domain');
        }
    }
    
    updateURLField(url) {
        const urlField = document.querySelector('#urlField');
        if (urlField) {
            urlField.value = url;
            console.log('Stimulus controller initialized for dropzone with ID:', this.dropzoneTarget.dataset.id);
        }
    }
    
    isValidURL(url) {
        return /^(https?:\/\/[^\s/$.?#].[^\s]*|ftp:\/\/[^\s]+|www\.[^\s]+|[^\s/$.?#]+\.[^\s]+(\/[^\s]*)?)$/i.test(url);
    }

}
