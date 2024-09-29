import {coreController} from './coreController.js';

export default class extends coreController {
    modal(event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Initialize stimulus attributes with default values
        this.initializeStimulusAtt();
        
        this.showModal({
            title: this.titleValue,
            text: this.textValue,
            icon: this.iconValue,
            showCancelButton: this.showCancelButtonValue,
            confirmButtonText: this.confirmButtonTextValue,
            preConfirm: () => this.handleRequestWithTimeout(() => this.fetch([])) // Use a function that returns a promise
        });
        // Automatically trigger the confirmation button after the first step
        swal.clickConfirm();
    }
    
    deleteConfirm(event) {
        event.preventDefault();
        event.stopPropagation();
        
        // Initialize stimulus attributes with default values
        // this.initializeDataAtt();
        this.initializeStimulusAtt();
        
        // const button = event.currentTarget;
        // const url = this.urlValue; // Valeur passée via stimulus_action
        // const token = button.closest('form').querySelector('input[name="_token"]').value;
        // const button = this.element.querySelector('button');
        const url = this.urlValue; //button.dataset.url;
        const token = this.element.querySelector('input[name="_token"]').value;
        
        this.showModal({
            title: this.titleValue,
            text: this.textValue,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            showLoaderOnConfirm: true,
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            preConfirm: () => this.handleRequestWithTimeout(() => this.deleteItem(url, token)) // Use a function that returns a promise
        });
    }
    
    showModal(options) {
        // Fonction générique pour afficher SweetAlert avec options personnalisées
        swal.fire({
            ...options,
            allowOutsideClick: false
        });
    }
    
    async fetch(data) {
        const response = await this.handleRequestWithTimeout(() => this.fetchForm(data)); // Pass a function that returns a promise
        return swal.fire({
            title: this.titleValue,
            html: response,
            showCancelButton: this.showCancelButtonValue,
            confirmButtonText: this.confirmButtonTextValue,
            allowOutsideClick: false,
            preConfirm: () => this.handleFormSubmit()
        });
    }
    
    // async handleRequestWithTimeout(requestFunc, timeout = 10000) {
    //     try {
    //         return await this.withTimeout(requestFunc(), timeout); // Call the function to execute the promise
    //     } catch (error) {
    //         swal.fire({
    //             icon: 'error',
    //             title: 'Request failed',
    //             text: error.message
    //         });
    //         throw error;
    //     }
    // }
    
    async handleRequestWithTimeout(requestFunc, timeout = 30000) { // Augmentez le timeout à 30 secondes
        const timeoutId = setTimeout(() => {
            throw new Error("Request timed out");
        }, timeout);
        
        try {
            const result = await this.withTimeout(requestFunc(), timeout);
            return result; // Retourne le résultat de la requête
        } catch (error) {
            swal.fire({
                icon: 'error',
                title: 'Request failed',
                text: error.message
            });
            throw error; // Rejette l'erreur pour le traitement en amont
        } finally {
            clearTimeout(timeoutId); // Arrête le timeout
        }
    }
    
    
    async handleFormSubmit() {
        return await this.handleRequestWithTimeout(() => this.submitForm()); // Pass a function that returns a promise
    }
    
    async deleteItem(url, token) {
        const controller = new AbortController(); // Crée un contrôleur d'abort
        const { signal } = controller;
        
        // Définissez un délai d'attente pour la requête
        const timeout = setTimeout(() => {
            controller.abort(); // Annule la requête si le délai est dépassé
        }, 5000); // Délai d'attente de 5 secondes
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'include',
                body: new URLSearchParams({ '_token': token }),
                signal // Ajoute le signal d'abort
            });
            
            // Si la réponse n'est pas OK, lance une erreur
            if (!response.ok) throw new Error('Erreur de suppression');
            
            // Affichez le message de succès
            toast.fire({
                icon: "success",
                title: "Supprimé! L'élément a été supprimé avec succès."
            });
            
            const id = this.idElementValue.split('@')[ 1 ]; // Extract the id
            const component = document.getElementById(this.idElementValue).__component; // Get the parent div
            // const component = divParent.querySelector(`[data-live-name-value="${id}"]`).__component; // Select the component
            // or call an action
            component.action('refresh', {'query': id});
            // then, trigger a re-render to get the fresh HTML
            // component.render();
            
        } catch (error) {
            if (error.name === 'AbortError') {
                swal.fire('Erreur', 'La requête a expiré. Veuillez réessayer.', 'error');
            } else {
                swal.fire('Erreur', error.message, 'error');
            }
        } finally {
            clearTimeout(timeout); // Efface le délai d'attente
        }
    }

    
    withTimeout(promise, ms) {
        const controller = new AbortController();
        const { signal } = controller;

        return Promise.race([
            promise, // Pass the promise directly now
            new Promise((_, reject) =>
                setTimeout(() => {
                    controller.abort();
                    reject(new Error("Request timed out"));
                }, ms)
            )
        ]);
    }
    
}