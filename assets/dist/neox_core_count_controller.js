import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    static targets = ['count']; // Déclare une cible pour afficher le nombre de clics
    
    connect() {
        this.clickCount = 0; // Initialise le compteur de clics
    }
    
    increment() {
        this.clickCount++; // Incrémente le compteur
        this.updateCountDisplay();
        this.increment();
    }
    
    updateCountDisplay() {
        // Met à jour l'affichage du compteur
        if (this.hasCountTarget) {
            this.countTarget.textContent = this.clickCount;
        }
    }
    
    increment() {
        // Envoyer les données au serveur
        fetch('/neox/dash/record-click', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ count: this.clickCount })
        })
        .then(response => {
            if (!response.ok) {
                console.error('Failed to record click');
            }
        });
    }
    
}

