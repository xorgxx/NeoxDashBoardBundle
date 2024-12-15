import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    static values = {
        interval: { type: Number, default: 30 } // Default to 30 minutes
    };
    
    connect() {
        const intervalInMilliseconds = this.intervalValue * 60 * 1000; // Convert minutes to milliseconds
        this.timeout = setTimeout(() => {
            this.refreshPage();
            this.logRefreshTime(); // Log the time before refreshing
        }, intervalInMilliseconds);
        
        this.setupEventListeners();
    }
    
    disconnect() {
        console.log(`Controller disconnected: ${this.identifier}`);
        this.removeEventListeners();
        clearTimeout(this.timeout); // Clear the timeout to avoid memory leaks
    }
    
    initialize() {
        console.log("Controller initialized");
    }
    
    refreshPage() {
        window.location.reload();
    }
    
    logRefreshTime() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString(); // Human-readable format, e.g., "14:30:25"
        console.log(`Page refreshed at: ${formattedTime}`);
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

