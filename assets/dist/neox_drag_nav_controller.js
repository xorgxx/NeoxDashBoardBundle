import {Controller} from '@hotwired/stimulus';

let url = null;
let TargetItem = null;

export default class extends Controller {
    static targets = ['dropzone'];
    
    initialize(){
        this.addEventListeners();
    }

    // Called when the controller is disconnected from the DOM
    disconnect(){
        // Remove event listeners if the dropzoneTarget exists
        if(this.hasOwnProperty('dropzoneTarget') && this.dropzoneTarget){
            const {dropzoneTarget} = this;
            dropzoneTarget.removeEventListener('dragover', this.handleDragOver.bind(this));
            dropzoneTarget.removeEventListener('dragleave', this.handleMouseOut.bind(this));
            dropzoneTarget.removeEventListener('drop', this.handleDrop.bind(this));
        } else {
            console.info('dropzoneTarget does not exist or is undefined.');
        }
    }
    
    // Adds the event listeners to the dropzone target
    addEventListeners(){
        const {dropzoneTarget} = this;
        dropzoneTarget.addEventListener('dragover', this.handleDragOver.bind(this));
        dropzoneTarget.addEventListener('dragleave', this.handleMouseOut.bind(this));
        dropzoneTarget.addEventListener('drop', this.handleDrop.bind(this));
    }
    
    // Handles the dragover event by adding a hover effect
    handleDragOver(event){
        event.preventDefault();
        this.dropzoneTarget.classList.add('dropzone-hover');
    }
    
    // Removes the hover effect on dragleave event
    handleMouseOut(){
        this.dropzoneTarget.classList.remove('dropzone-hover');
    }
    
    // Handles the drop event and processes each dropped item
    async handleDrop(event){
        event.preventDefault();
        
        const loader = document.getElementById('loader');
        const loading = document.getElementById('loading');
        
        loading.classList.add('no-select', 'body-loading'); // Add loading styles
        loader.style.display = 'block';
        
        // Get the URL from the dropzone data attribute
        this.TargetItem = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-nav-target="dropzone"]');
        
        const items = Array.from(event.dataTransfer.items);
        this.dropzoneTarget.classList.remove('dropzone-hover');
        
        // Sort items based on preferred types
        const preferredTypes = ['text/x-moz-url', 'text/plain', 'text/uri-list', 'text/html', 'text/x-moz-place'];
        items.sort((a, b) => preferredTypes.indexOf(a.type) - preferredTypes.indexOf(b.type));
        
        for(let item of items) {
            const shouldBreak = await this.processDrop(item);
            if(shouldBreak){
                break;
            }
        }
        
        this.cleanUp()
    }
    
    // Processes each dropped item based on its type and sends it if it's a valid URL
    processDrop(item){
        return new Promise((resolve) => {
            if(item?.kind !== 'string'){
                console.warn('Item is not of type "string".');
                return resolve(false);
            }
            
            item.getAsString(async(text) => {
                const cleanedText = text.trim();
                if(!cleanedText){
                    console.warn('Empty or invalid content.');
                    return resolve(false);
                }
                
                // Extract URLs based on item type
                let urls = item.type === '' ? cleanedText : this.extractURLs(item.type, cleanedText);
                
                if(urls.length > 0){
                    try {
                        await this.sendURLs(urls);
                        console.log('Data sent successfully:', urls);
                        resolve(true);
                    } catch(error) {
                        console.error('Error sending data:', error);
                        resolve(false);
                    }
                } else {
                    console.warn('No URLs to send.');
                    resolve(false);
                }
            });
        });
    }
    
    // Extracts URLs from the dropped item based on its type
    extractURLs(type, text){
        let urls = [];
        
        switch(type) {
            case 'text/plain':
                console.log('Dropped Plain Text:', text);
                urls.push(text);
                break;
            
            case 'text/uri-list':
            case 'text/x-moz-url':
                urls = text.split('\n').filter(url => url.trim() !== '');
                if(urls.length > 1){
                    console.log('Dropped URLs:', urls);
                } else {
                    console.warn('Type "text/uri-list" contains fewer than two URLs.');
                }
                break;
            
            case 'text/x-moz-place':
                try {
                    const bookmarkData = JSON.parse(text);
                    const bookmarkURL = bookmarkData.uri?.trim();
                    if(bookmarkURL){
                        console.log('Dropped Bookmark URL:', bookmarkURL);
                        urls.push(bookmarkURL);
                    } else {
                        console.warn('Empty or invalid bookmark URL.');
                    }
                } catch(error) {
                    console.error('Error parsing bookmark:', error);
                }
                break;
            
            case 'text/html':
                console.log('Dropped HTML:', text);
                urls.push(text);
                break;
            
            default:
                console.log('Unsupported type:', type);
                break;
        }
        
        return urls;
    }
    
    // Sends the URLs to the server using a POST request
    sendURLs(urls){
        const payload = {urls};
        const url = this.TargetItem.dataset.url
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if(response.ok){
                return response.json();
            } else {
                throw new Error('Request failed with status code ' + response.status);
            }
        })
        .then(data => {
            toast.fire({
                icon: "success",
                title: data // Display response message
            });
            
            const idElement = this.TargetItem.dataset.idElement;
            if ( idElement !== "element") {
                if (this.#isRelativeUrl(idElement)) {
                    Turbo.visit(idElement);
                } else {
                    const id = idElement.split('@')[1];
                    const component = document.getElementById(idElement).__component;
                    component.action('refresh', {'query': id});
                }
                return;
            }
            
            // const refreshButton = document.getElementById('refreshClass');
            // if (refreshButton) {
            //     refreshButton.click();
            // }
            
            
            return true;
        })
        .catch(error => {
            console.error("Error:", error);
            toast.fire({
                icon: "error",
                title: "An error occurred: " + error.message
            });
            return false;
        });
    }
    
    cleanUp(){
        // Hide the loader and restore opacity
        const loader = document.getElementById('loader');
        const loading = document.getElementById('loading');
        loader.style.display = 'none';
        loading.classList.remove('no-select', 'body-loading');
    }
    
    #isRelativeUrl(url){
        // Vérifie si la chaîne commence par "/", "./" ou "../", typiquement des indicateurs d'URL relative
        return url.startsWith('/') || url.startsWith('./') || url.startsWith('../');
    }
}
