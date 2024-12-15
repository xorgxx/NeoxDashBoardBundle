import {Controller} from '@hotwired/stimulus';
import PageRefresher from './inc/page-refresher.js';    // Importer la classe PageRefresher
import EventHandler from './inc/event-handler.js';      // Importer la classe EventHandler
import ClickCounter from './inc/click-counter.js';      // Importer la classe externalisée


export default class extends Controller {
    
    static values = {
        interval: {type: Number, default: 30} // Default to 30 minutes
    };
    
    static targets = ['count'];
    
    connect(){
        // Créer une instance de PageRefresher et démarrer
        this.pageRefresher = new PageRefresher(this, this.intervalValue);
        this.pageRefresher.start();
        
        // Créer une instance d'EventHandler et configurer les événements
        this.eventHandler = new EventHandler(this);
        this.eventHandler.setupEventListeners();
        
        this.clickCounter = new ClickCounter(this); // Crée une instance de ClickCounter
        this.clickCounter.reset();
    }
    
    disconnect(){
        console.log(`Controller disconnected: ${this.identifier}`);
        this.eventHandler.removeEventListeners();
        this.pageRefresher.stop(); // Arrêter le rafraîchissement
    }
    
    initialize(){
        console.log("Controller initialized");
    }
    
    /**
     * Déclenche une action sur un composant Stimulus s'il est disponible
     */
    triggerComponentAction(elementId, action, params = {}){
        const component = document.getElementById(elementId)?.__component;
        if(component){
            component.action(action, params);
        } else {
            console.warn(`No component found for element ID: ${elementId}`);
        }
    }
    
    increment(event) {
        // Récupérer l'élément déclencheur
        const jsonData = {
            url         : event.currentTarget.getAttribute('data-url'),
            domainId    : event.currentTarget.getAttribute('data-id-domain'),
            _token      : event.currentTarget.getAttribute('data-token'),
        };
        
        // Incrémenter le compteur et mettre à jour l'affichage
        this.clickCounter.increment();
        this.clickCounter.updateDisplay();
        
        // Exemple : envoyer les clics au serveur
        this.clickCounter.sendClickToServer(jsonData);
    }
}
