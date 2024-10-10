import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';

export class coreController extends Controller {
    static values = {
        // Valeurs par défaut pour SweetAlert
        showCancelButton: {type: Boolean, default: true},
        showConfirmButton: {type: Boolean, default: true},
        showDenyButton: {type: Boolean, default: false},
        denyButtonText: {type: String, default: 'Annuler'},
        cancelButtonText: {type: String, default: 'Fermer'},
        confirmButtonText: {type: String, default: 'Envoyer'},
        title: {type: String, default: 'Recherche ...'},
        text: {type: String, default: 'Recherche ...'},
        locale: {type: String, default: 'fr'},
        toast: {type: Boolean, default: false},
        background: {type: String, default: '#abd3c0'},
        icon: {type: String, default: 'info'},
        position: {type: String, default: 'center'},
        replie: {type: String, default: 'Recherche ...'},
        url: {type: String, default: 'Fetching ...'},
        idElement: {type: String, default: 'element'},
    };
    
    initializeStimulusAtt(){
        // Initialize values based on data- attributes
        this.#initializeStAttValues();
    }
    
    initializeDataAtt(){
        // Initialize values based on data- attributes
        this.#initializeDataAttValues();
    }
    
    async fetchForm(body, signal){
        
        swal.update({
            'text': `Loading form waiting ...`,
            'icon': "question"
        })
        swal.showLoading()
        
        let $f = JSON.stringify(body);
        return fetch(this.urlValue, {
            method: "post",
            body: $f,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal,
            credentials: 'include'
        })
        .then(await this.#handleResponse)
        .catch(this.#handleError)
    }
    
    async submitForm(signal) {
        const $elem = document.getElementById("swal2-html-container");
        const $form = $elem.querySelector("form");
        const formData = new FormData($form);
        const inputFields = [...$form.querySelectorAll('input, textarea, select')];
        
        // Vérification des champs obligatoires
        const formIsValid = inputFields.every(field => !field.hasAttribute('required') || field.value.trim());
        
        if (!formIsValid) {
            inputFields.forEach(field => {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.classList.add("is-invalid");
                } else {
                    field.classList.remove("is-invalid");
                }
            });
            
            // Afficher un message d'erreur et stopper l'exécution
            swal.showValidationMessage(`Veuillez remplir tous les champs obligatoires.`);
            return; // Empêcher la soumission si le formulaire est invalide
        }
        
        swal.update({
            'text': `En attente de soumission...`,
            'html': "",
            'icon': "question"
        });
        swal.showLoading();
        
        // Envoi de la requête avec les données du formulaire
        return fetch($form.action, {
            method: $form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            signal,
            credentials: 'include'
        })
        .then(this.#handleResponse)
        .then(data => {
            if (data == "true") {
                toast.fire({
                    icon: "success",
                    title: "Changements effectués avec succès"
                });
                $form.reset();
                
                const idElement = this.idElementValue;
                
                if (this.#isRelativeUrl(idElement)) {
                    Turbo.visit(idElement);
                } else {
                    const id = idElement.split('@')[1];
                    const component = document.getElementById(idElement).__component;
                    component.action('refresh', {'query': id});
                }
                
            } else {
                swal.fire({
                    title: this.titleValue,
                    html: data,
                    showCancelButton: this.showCancelButtonValue,
                    confirmButtonText: this.confirmButtonTextValue,
                    allowOutsideClick: false,
                    preConfirm: () => this.submitForm("formNeox"),
                });
                swal.showValidationMessage(`${error}`);
            }
        })
        .catch(this.#handleError);
    }
    
    async #handleResponse(result){
        if(!result.ok){
            throw new Error(`Network error: ${result.status} - ${result.statusText}`);
        }
        return result.text();
    }
    
    #isRelativeUrl(url) {
        // Vérifie si la chaîne commence par "/", "./" ou "../", typiquement des indicateurs d'URL relative
        return url.startsWith('/') || url.startsWith('./') || url.startsWith('../');
    }
    
    #handleError(error){
        swal.update({
            'text': `Request failed !!`,
            'icon': "warning"
        });
        swal.showValidationMessage(
            `${error}`
        );
    }
    
    // Get by stimulus attributes 
    #initializeStAttValues(){
        for(const key of Object.keys(this.constructor.values)) {
            // We use the Stimulus attributes
            const dataValue = this[ `${key}Value` ];
            
            // We apply the default value if no value is provided
            this[ key + 'Value' ] = dataValue || this.constructor.values[ key ].default;
        }
    }
    
    // Get by data-attributes
    #initializeDataAttValues() {
        for (const key of Object.keys(this.constructor.values)) {
            // Récupérer la valeur de l'attribut data-* correspondant
            const dataValue = this.element.dataset[this.#camelCaseToDash(key)];
            
            // Si la valeur data-* est fournie, on la convertit et l'utilise
            // Sinon, on utilise la valeur par défaut
            this[`${key}Value`] = dataValue !== undefined
                ? this.convertDataValue(dataValue)  // Convertir la valeur si nécessaire
                : this.constructor.values[key].default;
        }
    }

// Fonction utilitaire pour convertir le nom camelCase en format dash-case
    #camelCaseToDash(key) {
        return key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    }
    
    #convertDataValue(value){
        // Convert values to appropriate types
        if(value === 'true') return true;
        if(value === 'false') return false;
        const numberValue = Number(value);
        return isNaN(numberValue) ? value : numberValue;
    }
    
}