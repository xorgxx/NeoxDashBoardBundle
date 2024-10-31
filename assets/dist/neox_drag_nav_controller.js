import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dropzone'];
    
    connect() {
        this.addEventListeners();
    }
    
    disconnect() {
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
        this.DropItem = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-nav-target="dropzone"]');
        this.processDrop(item);
        this.handleMouseOut();
    }
    
    processDrop(item) {
        if (item?.kind === 'string') {
            item.getAsString((plainText) => {
                const text = plainText.trim().split('\n')[0];
                console.log('Dropped Text:', text);
                
                if (item.type === 'text/x-moz-place') {
                    const bookmarkData = JSON.parse(text);
                    const bookmarkURL = bookmarkData.uri;
                    console.log('Dropped Bookmark URL:', bookmarkURL);
                    this.validateAndProcessURL(bookmarkURL);
                } else {
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
        
        setTimeout(() => {
            dropzoneTarget.classList.remove('dropzone-valid', 'dropzone-invalid');
        }, 3000);
    }
    
    showError(message) {
        console.error(message);
        const initialContent = this.dropzoneTarget.innerHTML;
        
        // Utilisation d'un SVG inline pour l'icône d'erreur
        const errorIcon = `<svg width="20" height="20" fill="red" class="mx-2"><use href="#fa6-solid:exclamation-circle" /></svg>`;
        
        this.dropzoneTarget.innerHTML = `${errorIcon} ${message}`;
        this.dropzoneTarget.style.color = 'red';
        
        setTimeout(() => {
            this.dropzoneTarget.innerHTML = initialContent;
            this.dropzoneTarget.style.color = '';
        }, 5000);
    }
    
    processURL(url) {
        this.updateDropzoneStyle(true);
        
        const modalTriggerElement = this.DropItem;
        if (modalTriggerElement) {
            console.log('Stimulus controller initialized for dropzone with ID:', this.dropzoneTarget.dataset.id);
            modalTriggerElement.setAttribute('data-domain', url); // Update URL in a data-url attribute
            modalTriggerElement.click();
        }
    }
    
    isValidURL(url) {
        return /^(https?:\/\/[^\s/$.?#].[^\s]*|ftp:\/\/[^\s]+|www\.[^\s]+|[^\s/$.?#]+\.[^\s]+(\/[^\s]*)?)$/i.test(url);
    }
}
