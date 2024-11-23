import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.log(`Controller connected: ${this.identifier}`);
        this.setupEventListeners();
    }
    
    disconnect() {
        console.log(`Controller disconnected: ${this.identifier}`);
        this.removeEventListeners();
    }
    
    initialize() {
        console.log("Controller initialized");
    }
    
    /**
     * Configure les listeners d'événements
     */
    setupEventListeners() {
        addEventListener("turbo:before-stream-render", this.handleTurboStream.bind(this));
        addEventListener('favorite:refresh', this.handleFavoriteRefresh.bind(this));
    }
    
    /**
     * Supprime les listeners d'événements
     */
    removeEventListeners() {
        removeEventListener("turbo:before-stream-render", this.handleTurboStream.bind(this));
        removeEventListener('favorite:refresh', this.handleFavoriteRefresh.bind(this));
    }
    
    /**
     * Gestionnaire pour les événements "turbo:before-stream-render"
     */
    handleTurboStream(event) {
        const fallbackToDefaultActions = event.detail.render;
        
        event.detail.render = (streamElement) => {
            const idComponent   = streamElement.getAttribute("data-neox-idComponent");
            const idClass       = streamElement.getAttribute("data-neox-idClass");
            const action        = streamElement.getAttribute("data-neox-action");
            
            if (idComponent && action) {
                console.log("xorg wants to make a call to 🦖🦖");
                this.triggerComponentAction(idComponent, action, { query: idClass });
                
            } else {
            
            }
            // Si aucun attribut pertinent n'est trouvé, on exécute les actions par défaut
            fallbackToDefaultActions(streamElement);
        };
    }
    
    /**
     * Gestionnaire pour les événements "favorite:refresh"
     */
    handleFavoriteRefresh(event) {
        const { idComponent, idClass, action } = event.detail;
        
        if (idComponent && action) {
            this.triggerComponentAction(idComponent, action, { query: idClass });
        } else {
            console.warn(`Invalid parameters in favorite:refresh event`, event.detail);
        }
    }
    
    /**
     * Extrait l'ID d'un attribut au format "live-NeoxDashBoardContent@ID"
     */
    extractId(neoxId) {
        const parts = neoxId.split('@');
        return parts.length === 2 ? parts[1] : null;
    }
    
    /**
     * Déclenche une action sur un composant Stimulus s'il est disponible
     */
    triggerComponentAction(elementId, action, params = {}) {
        const component = document.getElementById(elementId)?.__component;
        
        if (component) {
            component.action(action, params);
        } else {
            console.warn(`No component found for element ID: ${elementId}`);
        }
    }
}

