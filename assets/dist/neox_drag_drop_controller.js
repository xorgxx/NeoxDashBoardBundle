import { Controller } from "@hotwired/stimulus";

let DraggedItem = null;

export default class extends Controller {
    static targets = ["item"]; // Cible tous les éléments à déplacer

    
    connect() {
        this.bindEvents();
    }
    
    disconnect() {
        this.unbindEvents();
    }
    
    bindEvents() {
        // Liaison des méthodes pour garder le bon contexte
        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleDrag = this.handleDrag.bind(this);
        this.handleDragEnd = this.handleDragEnd.bind(this);
        this.handleDragOver = this.handleDragOver.bind(this);
        this.handleDragLeave = this.handleDragLeave.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
        this.handleDragEnter = this.handleDragEnter.bind(this);
        
        // Écoute des événements de glisser-déposer
        this.element.addEventListener('dragstart', this.handleDragStart);
        this.element.addEventListener('drag', this.handleDrag);
        this.element.addEventListener('dragend', this.handleDragEnd);
        this.element.addEventListener('dragover', this.handleDragOver);
        this.element.addEventListener('dragleave', this.handleDragLeave);
        this.element.addEventListener('drop', this.handleDrop);
        this.element.addEventListener('dragenter', this.handleDragEnter);
    }
    
    unbindEvents() {
        // Suppression des événements de glisser-déposer
        this.element.removeEventListener('dragstart', this.handleDragStart);
        this.element.removeEventListener('drag', this.handleDrag);
        this.element.removeEventListener('dragend', this.handleDragEnd);
        this.element.removeEventListener('dragover', this.handleDragOver);
        this.element.removeEventListener('dragleave', this.handleDragLeave);
        this.element.removeEventListener('drop', this.handleDrop);
        this.element.removeEventListener('dragenter', this.handleDragEnter);
    }
    
    handleDragStart(event) {
        this.DraggedItem = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (this.DraggedItem) {
            event.dataTransfer.setData("text/plain", this.DraggedItem.dataset.site);
            // "section" | "domain-browser" | "domain-move"
            event.dataTransfer.setData("text/type", this.DraggedItem.dataset.type);
            event.dataTransfer.setData("text/id", this.DraggedItem.dataset.id);
            this.DraggedItem.classList.add('dragging'); // Added a visual effect during drag
        }
    }
    
    handleDragEnter(event) {
        event.preventDefault(); // Nécessaire pour permettre le drop
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            const draggedId = event.dataTransfer.getData("text/id");
            if (targetElement.dataset.id !== draggedId) {
                targetElement.classList.remove('dragging');
                targetElement.style.cursor = 'not-allowed';
            }
        }
    }
    
    handleDrag(event) {
        event.preventDefault(); // Nécessaire pour permettre le drop
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            const draggedId = event.dataTransfer.getData("text/id");
            if (targetElement.dataset.id !== draggedId) {
                targetElement.classList.add('drag-hover');
            }
        }
    }
    handleDragOver(event) {
        event.preventDefault(); // Nécessaire pour permettre le drop
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            const draggedId = event.dataTransfer.getData("text/id");
            if (targetElement.dataset.id !== draggedId) {
                targetElement.classList.add('drag-hover');
            }
        }
    }
    handleDragLeave(event) {
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            targetElement.classList.remove('drag-hover');
            targetElement.style.cursor = ''; // Réinitialiser le curseur
        }
    }
    handleDragEnd(event) {
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            targetElement.classList.remove('drag-hover');
            targetElement.classList.remove('dragging');
            targetElement.style.cursor = ''; // Réinitialiser le curseur
        }
    }
    
    async handleDrop(event) {
        event.preventDefault();
        
        const loader = document.getElementById('loader');
        const loading = document.getElementById('loading');
        // Afficher le loader et réduire l'opacité
        
        loading.classList.add('no-select', 'body-loading'); // Add loading styles
        loader.style.display = 'block';
        
        // Get dragged data
        const draggedElement = this.DraggedItem;
        const draggedId = event.dataTransfer.getData("text/id");
        
        // Get current target data
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        
        if (targetElement) {
            // "section" | "domain-browser" | "domain-move"
            const type = targetElement.dataset.type;
            const targetId = targetElement.dataset.id;
            const targetApi = targetElement.dataset.api;
            
            // log for dev ==========
            console.log(`Type:  ${type} Dragged ID: ${draggedId}, Dropped On ID: ${targetId}, Api: ${targetApi}`);
            targetElement.classList.remove('drag-hover', 'dragging');
            
            // if id different we do
            if (draggedId !== targetId) {
                try {
                    switch (type) {
                        case 'section':
                            targetElement.insertAdjacentElement('beforebegin', draggedElement);
                            await this.updateEntity(draggedId, targetId, targetApi);
                            break;
                        
                        case 'domain-browser':
                            // Logic for 'domain-browser' can be added here if needed
                            await this.updateEntity(draggedId, targetId, targetApi);
                            break;
                        
                        case 'domain-move':
                            // Move the dragged element before the target element
                            targetElement.insertAdjacentElement('beforebegin', draggedElement);
                            await this.updateEntity(draggedId, targetId, targetApi);
                            break;
                        
                        default:
                            // Optionally handle any other types if needed
                            await this.updateEntity(draggedId, targetId, targetApi);
                            break;
                    }
                } catch (error) {
                    console.error('Failed to update entity:', error);
                }
            }
            
            this.cleanUp(targetElement)
        }
    }
    
    
    // handleDrop(event) {
    //     event.preventDefault();
    //
    //     const loader = document.getElementById('loader');
    //     // Afficher le loader et réduire l'opacité
    //     loader.style.display = 'block';
    //     document.body.classList.add('no-select', 'body-loading'); // Add loading styles
    //
    //     // Get dragged data
    //     const draggedElement      = this.DraggedItem;
    //     const draggedId     = event.dataTransfer.getData("text/id")
    //
    //     // Get current target data
    //     const targetElement       = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
    //
    //     if (targetElement) {
    //         // "section" | "domain-browser" | "domain-move"
    //         const type      = targetElement.dataset.type;
    //         const targetId  = targetElement.dataset.id;
    //         const targetApi = targetElement.dataset.api;
    //         // log for dev ==========
    //         console.log(`Type:  ${type} Dragged ID: ${draggedId}, Dropped On ID: ${targetId}, Api: ${targetApi}`);
    //         // if id deffrent we do !!
    //         if( draggedId !== targetId ){
    //             switch (type) {
    //                 case 'section':
    //                     targetElement.insertAdjacentElement('beforebegin', draggedElement);
    //                     this.updateEntity(draggedId, targetId, targetApi)
    //                     break;
    //
    //                 case 'domain-browser':
    //
    //                     break;
    //
    //                 case 'domain-move':
    //                     // Move the dragged element before the target element
    //                     targetElement.insertAdjacentElement('beforebegin', draggedElement);
    //                     this.updateEntity(draggedId, targetId, targetApi);
    //                     break;
    //
    //                 default:
    //                     // Optionally handle any other types if needed
    //                     break;
    //             }
    //         }
    //
    //     }
    // }
    
    updateCursorStyle(targetElement) {
        // Mise à jour du style du curseur en fonction du type d'élément
        const itemType = targetElement.dataset.type;
        switch (itemType) {
            case 'tab':
                targetElement.style.cursor = 'move'; // Curseur pour les onglets
                break;
            case 'domain':
                targetElement.style.cursor = 'copy'; // Curseur pour les domaines
                break;
            default:
                targetElement.style.cursor = 'not-allowed';
                break;
        }
    }
    
    async updateEntity(draggedId, targetId, targetApi) {
        try {
            const response = await fetch(targetApi, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ draggedId, targetId })
            });
            
            if (!response.ok) {
                throw new Error(`Error: ${response.status}`);
            }
            
            const data = await response.json();
            console.log("Entity updated:", data);
            return data; // Return data if needed for further processing
        } catch (error) {
            console.error('Error updating entity:', error);
            throw error; // Re-throw the error for handling in the caller
        }
    }
    
    
    // updateEntity(draggedId, targetId, targetApi) {
    //     // Logic to update order via API for tabs
    //     fetch(targetApi, {
    //         method: 'POST',
    //         headers: { 'Content-Type': 'application/json' },
    //         body: JSON.stringify({ draggedId, targetId })
    //     })
    //     .then(response => response.ok ? response.json() : Promise.reject(response.status))
    //     .then(data => console.log("Entity updated:", data))
    //     .catch(error => console.error('Error updating entity:', error));
    // }
    
    cleanUp() {
        // Hide the loader and restore opacity
        const loader = document.getElementById('loader');
        const loading = document.getElementById('loading');
        loader.style.display = 'none';
        loading.classList.remove('no-select', 'body-loading');
    }
}
