import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dropzone'];
    
    connect() {
        this.isProcessing = false; // Définir isProcessing dans connect()
        this.addEventListeners();
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
    
    processDrop(item) {
        if (item?.kind === 'string') {
            item.getAsString((plainText) => {
                const url = plainText.trim().split('\n')[0];
                console.log('Dropped URL:', url);
                this.validateAndProcessURL(url);
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
        
        // Rétablir le style initial après 3 secondes
        setTimeout(() => {
            dropzoneTarget.classList.remove('dropzone-valid', 'dropzone-invalid');
        }, 3000);
    }
    
    showError(message) {
        console.error(message);
        const initialContent = this.dropzoneTarget.innerHTML; // Utiliser innerHTML pour conserver les balises HTML
        
        // Ajoutez une icône d'erreur ici, par exemple une icône de croix
        const errorIcon = '<twig:ux:icon name="fa6-solid:exclamation-circle" width="20" height="20" color="red" class="mx-2" />';
        
        // Mettre à jour le contenu avec le message d'erreur et l'icône
        this.dropzoneTarget.innerHTML = `${errorIcon} ${message}`;
        this.dropzoneTarget.style.color = 'red'; // Changer la couleur du texte pour indiquer l'erreur
        
        // Restaurer le contenu et la couleur après 5 secondes
        setTimeout(() => {
            this.dropzoneTarget.innerHTML = initialContent; // Restaurer le contenu initial
            this.dropzoneTarget.style.color = ''; // Remettre la couleur initiale
        }, 5000);
    }
    
    processURL(url) {
        this.updateURLField(url);
        this.updateDropzoneStyle(true);
        const modalTriggerElement = this.element.closest('[data-action="click->xorgxx--neox-dashboard-bundle--neox-modal#modal"]');
        if (modalTriggerElement) {
            modalTriggerElement.setAttribute('data-domain', url); // Mettre à jour l'URL dans un attribut data-url
            modalTriggerElement.click(); // Déclenche l'événement click
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
