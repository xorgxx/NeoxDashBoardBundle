import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["item"];
    
    connect(){
        // Liez les méthodes aux événements pour les écouter
        this.handleDragStart    = this.handleDragStart.bind(this);
        // this.handleDragOver     = this.handleDragOver.bind(this);
        // this.handleDragLeave    = this.handleDragLeave.bind(this);
        // Attachez les écouteurs d'événements
        this.element.addEventListener('dragstart', this.handleDragStart);
        // this.element.addEventListener('dragover', this.handleDragOver);
        // this.element.addEventListener('dragleave', this.handleDragLeave);
    }
    
    disconnect(){
        // Supprimez les écouteurs d'événements lors de la déconnexion
        this.element.removeEventListener('dragstart', this.handleDragStart);
        // this.element.removeEventListener('dragover', this.handleDragOver);
        // this.element.removeEventListener('dragleave', this.handleDragLeave);
    }
    
    handleDragStart(event){
        // Vérifiez si l'élément draggable est la cible
        const item = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        event.dataTransfer.setData("type", item.dataset.type);
        event.dataTransfer.setData("text/plain", item.dataset.site);
        item.classList.add('dragging'); // Add visual effect during drag
 
    }
    
    handleDragOver(event){
        event.preventDefault();
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const type = event.dataTransfer.getData("type");
        if(type== "domain-move"){
            return
        }
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        if(domainChild){
            domainChild.classList.add('drag-hover'); // Add hover effect during drag on the child
        }
    }
    
    handleDragLeave(event){
        const targetElement = event.target.closest('[data-xorgxx--neox-dashboard-bundle--neox-drag-domain-target="item"]');
        const domainChild = targetElement.querySelector('.domain'); // Find child with class 'domain'
        if(domainChild){
            domainChild.classList.remove('drag-hover'); // Remove hover effect during drag on the child
        }
    }
}
