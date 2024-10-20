import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["item"];
    
    connect(){
        this.itemTargets.forEach(item => {
            item.addEventListener('dragstart', this.handleDragStart.bind(this));
            item.addEventListener('dragend', this.handleDragEnd.bind(this));
        });
        
        this.element.addEventListener('dragover', this.handleDragOver.bind(this));
        this.element.addEventListener('dragleave', this.handleDragLeave.bind(this));
        this.element.addEventListener('drop', this.handleDrop.bind(this));
    }
    
    handleDragStart(event){
        event.dataTransfer.setData("text/plain", event.target.dataset.id);
        event.target.classList.add('dragging'); // Add visual effect during drag
    }
    
    handleDragEnd(event){
        event.target.classList.remove('dragging'); // Remove visual effect after drag
    }
    
    handleDragOver(event){
        event.preventDefault();
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        if(domainChild){
            domainChild.classList.add('drag-hover'); // Add hover effect during drag on the child
        }
    }
    
    handleDragLeave(event){
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        if(domainChild){
            domainChild.classList.remove('drag-hover'); // Add hover effect during drag on the child
        }
    }
    
    // handleDrop(event){
    //     event.preventDefault();
    //
    //     const draggedId = event.dataTransfer.getData("text/plain");
    //     const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
    //
    //     if(targetElement){
    //         const targetId = targetElement.dataset.id;
    //         const targetApi = targetElement.dataset.api;
    //         console.log(`Dragged ID: ${draggedId}, Dropped On ID: ${targetId}, Api: ${targetApi}`);
    //
    //         // Send data to the server
    //         this.sendData(draggedId, targetId, targetApi);
    //         // Remove hover effect from the target element
    //         const domainChild   = targetElement.querySelector('.domain');
    //         if(domainChild){
    //             domainChild.classList.remove('drag-hover');
    //         }
    //     } else {
    //         console.log(`Dragged ID: ${draggedId}, Dropped On ID: undefined`);
    //     }
    // }
    handleDrop(event) {
        event.preventDefault();
        
        const draggedId = event.dataTransfer.getData("text/plain");
        const draggedElement = document.getElementById("neox_dash_domain_"+draggedId);
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        
        if (targetElement) {
            const targetId = targetElement.dataset.id;
            const targetApi = targetElement.dataset.api;
            console.log(`Dragged ID: ${draggedId}, Dropped On ID: ${targetId}, Api: ${targetApi}`);
            
            // Move the dragged element after the target element
            targetElement.insertAdjacentElement('beforebegin', draggedElement);
       
            // Send data to the server to update order or perform backend tasks
            this.sendData(draggedId, targetId, targetApi);
            
            // Adjust the positions of other elements
            // this.updatePositions();
            
            // Remove hover effect from the target element
            const domainChild = targetElement.querySelector('.domain');
            if (domainChild) {
                domainChild.classList.remove('drag-hover');
            }
            
            
            
        } else {
            console.log(`Dragged ID: ${draggedId}, Dropped On ID: undefined`);
        }
    }
    
    updatePositions(draggedItemId) {
        const items = document.querySelectorAll('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        let draggedItemIndex;
        
        // Parcours des éléments pour identifier leur nouvelle position
        items.forEach((item, index) => {
            const id = item.dataset.id;
            
            // Si c'est l'élément déplacé, on le marque
            if (id === draggedItemId) {
                draggedItemIndex = index;
            }
        });
        
        // Maintenant, nous mettons à jour les positions à partir de l'élément déplacé
        items.forEach((item, index) => {
            const id = item.dataset.id;
            
            // Si l'élément est après l'élément déplacé, on décale sa position
            if (index >= draggedItemIndex) {
                const newPosition = index + 1; // Décalage de 1 pour chaque élément après l'élément déplacé
                console.log(`Item ID: ${id}, New Position: ${newPosition}`);
                
                // Envoyer la mise à jour au serveur si nécessaire
                this.sendPositionUpdate(id, newPosition);
            }
        });
    }


    
    sendPositionUpdate(id, newPosition) {
        // Logic to send the updated positions to the server
        console.log(`Updating server: ID = ${id}, New Position = ${newPosition}`);
    }
    
    sendData(draggedId, targetId, targetApi) {
        // Logic to send data to the server regarding the drop
        console.log(`Sending data: draggedId = ${draggedId}, targetId = ${targetId}, targetApi = ${targetApi}`);
    }
    
    async sendData(draggedId, targetId, targetApi) {
        const loader = document.getElementById('loader');
        const body = document.querySelector('body'); // Ou n'importe quel élément à rendre semi-transparent
        
        try {
            // Afficher le loader et réduire l'opacité
            loader.style.display = 'block';
            body.classList.add('loading-opacity');
            
            const response = await fetch(targetApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ draggedId, targetId }),
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Response from server:', data);
        } catch (error) {
            console.error('Error during fetch:', error);
        } finally {
            // Masquer le loader et restaurer l'opacité
            loader.style.display = 'none';
            body.classList.remove('loading-opacity');
        }
    }

    
}
