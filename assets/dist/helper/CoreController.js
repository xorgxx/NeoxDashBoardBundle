// assets/javascript/controllers/coreController.js
import { Controller } from '@hotwired/stimulus';
import ModalHandler from './modalHandler.js';
import FormHandler from './formHandler.js';

export class coreController extends Controller {
    static values = {
        showCancelButton: { type: Boolean, default: true },
        showConfirmButton: { type: Boolean, default: true },
        showDenyButton: { type: Boolean, default: false },
        denyButtonText: { type: String, default: 'Annuler' },
        cancelButtonText: { type: String, default: 'Fermer' },
        confirmButtonText: { type: String, default: 'Envoyer' },
        title: { type: String, default: 'Recherche ...' },
        text: { type: String, default: 'Recherche ...' },
        locale: { type: String, default: 'fr' },
        toast: { type: Boolean, default: false },
        background: { type: String, default: '#abd3c0' },
        icon: { type: String, default: 'info' },
        position: { type: String, default: 'center' },
        replie: { type: String, default: 'Recherche ...' },
        url: { type: String, default: 'Fetching ...' },
        idElement: { type: String, default: 'element' },
    };
    
    initialize() {
        this.initializeStimulusAtt();
        this.modalHandler = new ModalHandler(swal);
        this.formHandler = new FormHandler(this.element, swal); // Passer l'élément et swal à FormHandler
    }
    
    initializeStimulusAtt() {
        this.#initializeStAttValues();
    }
    
    async fetchForm(body, signal) {
        this.modalHandler.showLoading('Loading form waiting ...');
        
        return fetch(this.urlValue, {
            method: 'POST',
            body: JSON.stringify(body),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal,
            credentials: 'include'
        })
        .then(this.modalHandler.handleResponse)
        .catch(this.modalHandler.handleError);
    }
    
    async deleteConfirm(event) {
        event.preventDefault();
        
        const button = this.element.querySelector('button');
        const url = button.dataset.url;
        const token = this.element.querySelector('input[name="_token"]').value;
        
        this.modalHandler.showModal({
            title: button.dataset.title,
            text: button.dataset.text,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            showLoaderOnConfirm: true,
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            preConfirm: () => this.handleRequestWithTimeout(() => this.deleteItem(url, token))
        });
    }
    
    async deleteItem(url, token) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ '_token': token })
        });
        
        if (!response.ok) throw new Error('Erreur de suppression');
        
        this.modalHandler.showModal({
            title: 'Supprimé!',
            text: 'L\'élément a été supprimé avec succès.',
            icon: 'success'
        });
    }
    
    async withTimeout(promise, ms) {
        const controller = new AbortController();
        const { signal } = controller;
        
        return Promise.race([
            promise,
            new Promise((_, reject) =>
                setTimeout(() => {
                    controller.abort();
                    reject(new Error("Request timed out"));
                }, ms)
            )
        ]);
    }
    
    #initializeStAttValues() {
        for (const key of Object.keys(this.constructor.values)) {
            const dataValue = this[`${key}Value`];
            this[`${key}Value`] = dataValue || this.constructor.values[key].default;
        }
    }
}
