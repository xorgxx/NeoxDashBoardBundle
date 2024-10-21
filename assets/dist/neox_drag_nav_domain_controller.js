import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["item"];
    
    connect(){
        // Liez les méthodes aux événements pour les écouter
        this.handleDragStart    = this.handleDragStart.bind(this);
        
        // Attachez les écouteurs d'événements
        this.element.addEventListener('dragstart', this.handleDragStart);
    }
    
    disconnect(){
        // Supprimez les écouteurs d'événements lors de la déconnexion
        this.element.removeEventListener('dragstart', this.handleDragStart);
    }
    
    handleDragStart(event){
        // Vérifiez si l'élément draggable est la cible
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        if(item){
            event.dataTransfer.setData("text/plain", item.dataset.site);
            item.classList.add('dragging'); // Add visual effect during drag
        }
    }
}
