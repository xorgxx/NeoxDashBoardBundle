import { Controller } from '@hotwired/stimulus';

let url = null;
export default class extends Controller {
    static targets = ['dropzone'];
    
    connect() {
        this.addEventListeners();
    }
    
    disconnect() {
        if (this.hasOwnProperty('dropzoneTarget') && this.dropzoneTarget) {
            const { dropzoneTarget } = this;
            dropzoneTarget.removeEventListener('dragover', this.handleDragOver.bind(this));
            dropzoneTarget.removeEventListener('dragleave', this.handleMouseOut.bind(this));
            dropzoneTarget.removeEventListener('drop', this.handleDrop.bind(this));
        } else {
            console.info('dropzoneTarget n\'existe pas ou est indéfini.');
        }
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
    
    async handleDrop(event) {
        event.preventDefault();
  
        const url  = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-nav-target="dropzone"]');
        
        if(url){
            this.url = url.dataset.url;
        }
        const items = event.dataTransfer.items;
        this.dropzoneTarget.classList.remove('dropzone-hover');
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const shouldBreak = await this.processDrop(item);
            if (shouldBreak) {
                break; // Sort de la boucle si processDrop retourne true
            }
        }
    }
    
    processDrop(item) {
        return new Promise((resolve) => {
            if (item?.kind !== 'string') {
                console.warn('L\'élément n\'est pas de type "string".');
                return resolve(false);
            }
            
            item.getAsString((text) => {
                const cleanedText = text.trim();
                if (!cleanedText) {
                    console.warn('Contenu vide ou invalide.');
                    return resolve(false);
                }
                
                let urls = this.extractURLs(item.type, cleanedText);
                
                if (urls.length > 0) {
                    this.sendURLs(urls)
                    .then(() => {
                        console.log('Données envoyées avec succès:', urls);
                        resolve(true);
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'envoi des données:', error);
                        resolve(false);
                    });
                } else {
                    console.warn('Aucune URL à envoyer.');
                    resolve(false);
                }
            });
        });
    }
    
    extractURLs(type, text) {
        let urls = [];
        
        switch (type) {
            case 'text/plain':
                console.log('Dropped Plain Text:', text);
                urls.push(text);
                break;
            
            case 'text/uri-list':
                urls = text.split('\n').filter(url => url.trim() !== '');
                if (urls.length > 1) {
                    console.log('Dropped URI List URLs:', urls);
                } else {
                    console.warn('Liste d\'URI ne contient qu\'une seule URL ou est vide.');
                }
                break;
            
            case 'text/x-moz-place':
                try {
                    const bookmarkData = JSON.parse(text);
                    const bookmarkURL = bookmarkData.uri?.trim();
                    if (bookmarkURL) {
                        console.log('Dropped Bookmark URL:', bookmarkURL);
                        urls.push(bookmarkURL);
                    } else {
                        console.warn('Bookmark URL vide ou invalide.');
                    }
                } catch (error) {
                    console.error('Erreur lors de l\'analyse de bookmark:', error);
                }
                break;
            
            case 'text/html':
                console.log('Dropped HTML:', text);
                urls.push(text);
                break;
            
            case 'text/x-moz-url':
                urls = text.split('\n').filter(url => url.trim() !== '');
                if (urls.length > 1) {
                    console.log('Dropped URLs:', urls);
                } else {
                    console.warn('Type "text/x-moz-url" contient moins de deux URL.');
                }
                break;
            
            default:
                console.log('Type non pris en charge:', type);
                break;
        }
        
        return urls;
    }
    
    sendURLs(urls) {
        const payload = { urls };
        return fetch(this.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (response.ok) {
                // Affiche un toast de succès avec le message reçu
                return response.json(); // Supposons que la réponse contient des données JSON
            } else {
                throw new Error('La requête a échoué avec le code de statut ' + response.status);
            }
        })
        .then(data => {
            // Affiche le toast si la requête est réussie
            toast.fire({
                icon: "success",
                title: data // Utilise la clé 'message' du JSON de la réponse
            });
            
            // Actualise la page si le bouton est présent
            const refreshButton = document.getElementById('refreshClass');
            if (refreshButton) {
                refreshButton.click();
            }
            
            return true;
        })
        .catch(error => {
            console.error("Erreur:", error);
            // Affiche un toast d'erreur
            toast.fire({
                icon: "error",
                title: "Une erreur est survenue : " + error.message
            });
        });

    }
    
}
