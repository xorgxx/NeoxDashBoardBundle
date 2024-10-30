import { Controller } from "@hotwired/stimulus";

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
        // this.handleDrag = this.handleDrag.bind(this);
        // this.handleDragEnd = this.handleDragEnd.bind(this);
        this.handleDragOver = this.handleDragOver.bind(this);
        // this.handleDragLeave = this.handleDragLeave.bind(this);
        // this.handleDrop = this.handleDrop.bind(this);
        // this.handleDragEnter = this.handleDragEnter.bind(this);
        
        // Écoute des événements de glisser-déposer
        this.element.addEventListener('dragstart', this.handleDragStart);
        // this.element.addEventListener('drag', this.handleDrag);
        // this.element.addEventListener('dragend', this.handleDragEnd);
        this.element.addEventListener('dragover', this.handleDragOver);
        // this.element.addEventListener('dragleave', this.handleDragLeave);
        // this.element.addEventListener('drop', this.handleDrop);
        // this.element.addEventListener('dragenter', this.handleDragEnter);
    }
    
    unbindEvents() {
        // Suppression des événements de glisser-déposer
        // this.element.removeEventListener('dragstart', this.handleDragStart);
        // this.element.removeEventListener('drag', this.handleDrag);
        // this.element.removeEventListener('dragend', this.handleDragEnd);
        // this.element.removeEventListener('dragover', this.handleDragOver);
        // this.element.removeEventListener('dragleave', this.handleDragLeave);
        // this.element.removeEventListener('drop', this.handleDrop);
        // this.element.removeEventListener('dragenter', this.handleDragEnter);
    }
    
    handleDragStart(event) {
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (item) {
            event.dataTransfer.setData("text/type", item.dataset.type);
            event.dataTransfer.setData("text/url", item.dataset.id);
            event.dataTransfer.setData("text/plain", item.dataset.site);
            item.classList.add('dragging'); // Ajout d'un effet visuel pendant le drag
        }
    }
    
    handleDragEnd(event) {
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (item) {
            item.classList.remove('dragging');
        }
    }
    
    handleDrag(event) {
        event.preventDefault(); // Empêche le comportement par défaut
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        
        if (targetElement) {
            const draggedId = event.dataTransfer.getData("text/url");
            if (targetElement.dataset.id !== draggedId) {
                targetElement.classList.add('drag-hover');
                this.updateCursorStyle(targetElement);
            } else {
                targetElement.style.cursor = 'not-allowed';
            }
        } else {
            document.body.style.cursor = 'not-allowed';
        }
    }
    
    handleDragOver(event) {
        event.preventDefault(); // Nécessaire pour permettre le drop
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        
        if (targetElement) {
            const draggedId = event.dataTransfer.getData("text/url");
            if (targetElement.dataset.id !== draggedId) {
                targetElement.classList.add('drag-hover');
            } else {
            
            }
            this.updateCursorStyle(targetElement);
        } else {
            document.body.style.cursor = 'not-allowed';
        }
    }
    
    handleDragLeave(event) {
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        if (targetElement) {
            targetElement.classList.remove('drag-hover');
            targetElement.style.cursor = ''; // Réinitialiser le curseur
        }
        document.body.style.cursor = ''; // Réinitialiser le curseur global
    }
    
    handleDrop(event) {
        event.preventDefault();
        const draggedId = event.dataTransfer.getData("text/plain");
        const draggedElement = document.getElementById(`item-${draggedId}`);
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-drop-target="item"]');
        document.body.style.cursor = 'not-allowed';
        if (targetElement) {
            const type = targetElement.dataset.type; // "tab" ou "domain"
            const targetId = targetElement.dataset.id;
            const targetApi = targetElement.dataset.api;
            // targetElement.style.cursor = ''; // Réinitialiser le curseur
            
            if (type === 'tab') {
                targetElement.insertAdjacentElement('beforebegin', draggedElement);
                this.updateOrder(draggedId, targetId, targetApi);
            } else if (type === 'domain') {
                this.updateSection(draggedId, targetId, targetApi);
            }
            
            targetElement.classList.remove('drag-hover');
        }
    }
    
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
    
    updateOrder(draggedId, targetId, targetApi) {
        // Logic to update order via API for tabs
        fetch(targetApi, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ draggedId, targetId })
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.status))
        .then(data => console.log("Order updated:", data))
        .catch(error => console.error('Error updating order:', error));
    }
    
    updateSection(draggedId, targetId, targetApi) {
        // Logic to update section via API for domains
        fetch(targetApi, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ draggedId, targetId })
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.status))
        .then(data => console.log("Section updated:", data))
        .catch(error => console.error('Error updating section:', error));
    }
}
