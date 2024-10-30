import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["item"];
    
    connect(){
        // Liez les méthodes aux événements pour les écouter
        this.handleDragStart    = this.handleDragStart.bind(this);
        this.handleDragEnd      = this.handleDragEnd.bind(this);
        this.handleDragOver     = this.handleDragOver.bind(this);
        this.handleDragLeave    = this.handleDragLeave.bind(this);
        this.handleDrop         = this.handleDrop.bind(this);
        
        // Attachez les écouteurs d'événements
        this.element.addEventListener('dragstart', this.handleDragStart);
        this.element.addEventListener('dragend', this.handleDragEnd);
        this.element.addEventListener('dragover', this.handleDragOver);
        this.element.addEventListener('dragleave', this.handleDragLeave);
        this.element.addEventListener('drop', this.handleDrop);
    }
    
    disconnect(){
        // Supprimez les écouteurs d'événements lors de la déconnexion
        this.element.removeEventListener('dragstart', this.handleDragStart);
        this.element.removeEventListener('dragend', this.handleDragEnd);
        this.element.removeEventListener('dragover', this.handleDragOver);
        this.element.removeEventListener('dragleave', this.handleDragLeave);
        this.element.removeEventListener('drop', this.handleDrop);
    }
    
    handleDragStart(event){
        // Vérifiez si l'élément draggable est la cible
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        event.dataTransfer.setData("type", item.data.type);
        if(item){
            event.dataTransfer.setData("text/url", item.dataset.id);   // URL associée
            event.dataTransfer.setData("text/plain", item.dataset.site);
            item.classList.add('dragging'); // Add visual effect during drag
        }
    }
    
    handleDragEnd(event){
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        if(item){
            item.classList.remove('dragging'); // Remove visual effect after drag
        }
    }
    
    handleDragOver(event) {
        event.preventDefault();
        const type = event.dataTransfer.getData("type");
        
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        
        if (domainChild && type === "domain-move") {
            // Add hover effect during drag on the child
            domainChild.classList.add('drag-hover');
            
            // Change cursor style when dragging over
            domainChild.style.cursor = 'copy'; // Example: copy cursor for "domain-browser" type
        } else if (domainChild) {
            // Reset to default cursor for other types
            domainChild.style.cursor = 'not-allowed';
        }
    }
    
    
    handleDragLeave(event){
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        if(domainChild){
            domainChild.classList.remove('drag-hover'); // Remove hover effect during drag on the child
        }
    }
    
    handleDrop(event){
        event.preventDefault();
        
        const draggedId = event.dataTransfer.getData("text/url");
        const draggedElement = document.getElementById("neox_dash_domain_" + draggedId);
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        
        if(targetElement){
            const targetId = targetElement.dataset.id;
            const targetApi = targetElement.dataset.api;
            console.log(`Dragged ID: ${draggedId}, Dropped On ID: ${targetId}, Api: ${targetApi}`);
            
            // Move the dragged element before the target element
            targetElement.insertAdjacentElement('beforebegin', draggedElement);
            
            // Send data to the server to update order or perform backend tasks
            this.sendData(draggedId, targetId, targetApi);
            
            // Remove hover effect from the target element
            const domainChild = targetElement.querySelector('.domain');
            if(domainChild){
                domainChild.classList.remove('drag-hover');
            }
        } else {
            console.log(`Dragged ID: ${draggedId}, Dropped On ID: undefined`);
        }
    }
    
    sendData(draggedId, targetId, targetApi){
        const loader = document.getElementById('loader');
        const body = document.getElementById('bbbbb'); // Ou n'importe quel élément à rendre semi-transparent
        
        // Afficher le loader et réduire l'opacité
        loader.style.display = 'block';
        body.classList.add('no-select', 'body-loading');
        
        fetch(targetApi, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({draggedId, targetId}),
        })
        .then(response => {
            if(!response.ok){
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Gérer la réponse ici si nécessaire
            const refreshButton = document.getElementById('refreshClass');
            if(refreshButton){
                refreshButton.click(); // Simulate a button click
            }
        })
        .catch(error => {
            console.error('Error during fetch:', error);
        })
        .finally(() => {
            // Masquer le loader et restaurer l'opacité
            loader.style.display = 'none';
            body.classList.remove('no-select', 'body-loading');
        });
    }
}
