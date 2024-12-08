import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';

export class coreDashController extends Controller {
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
        timeout: {type: Number, default: 15000},
    };
    
    async fetchForm(body){
        
        swal.update({
            'text': `Loading form waiting ...`,
            'icon': "question"
        })
        swal.showLoading()
        
        let $f = JSON.stringify(body);
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            return {type: 'timeout', message: 'timeout'};
        }, this.timeoutValue); // Set timeout to 5 seconds
        
        try {
            const response = await fetch(this.urlValue, {
                method: "post",
                body: $f,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include',
                signal: controller.signal
            });
            clearTimeout(timeoutId);
            return await this.#handleResponse(response);
        } catch (error) {
            if(error.name === 'AbortError'){
                return {type: 'error', message: `timeout : ${this.timeoutValue / 1000} secondes` };
            }
            this.#handleError(error);
        }
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
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            swal.fire({
                title: this.titleValue,
                html: `timeout : ${this.timeoutValue / 1000} secondes`,
                showCancelButton: true,
                showConfirmButton: false,
                preConfirm: () => this.submitForm("formNeox"),
            });
        }, this.timeoutValue); // Set timeout to 5 seconds
        
        swal.update({
            'text': `En attente de soumission...`,
            'html': "",
            'icon': "question"
        });
        
        swal.showLoading();
        
        try {
            const response = await fetch($form.action, {
                method: $form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                signal: controller.signal,
                credentials: 'include'
            });
            clearTimeout(timeoutId);
            const r = await this.#handleResponse(response);
            if (r.status === 200) {
                toast.fire({
                    icon: "success",
                    title: "Changements effectués avec succès"
                });
                $form.reset();
                
                if (this.idElementValue !== "element") {
                    const idElement = this.idElementValue;
                    if (this.#isRelativeUrl(idElement)) {
                        Turbo.visit(idElement);
                    } else {
                        const id = idElement.split('@')[1];
                        const component = document.getElementById(idElement).__component;
                        component.action('refresh', {'query': id});
                    }
                    return;
                }
            } else {
                swal.fire({
                    title: this.titleValue,
                    html: r.data,
                    showCancelButton: this.showCancelButtonValue,
                    confirmButtonText: this.confirmButtonTextValue,
                    denyButtonText: this.denyButtonTextValue,
                    preConfirm: () => this.submitForm("formNeox"),
                });
            }
        } catch (error) {
            if(error.name === 'AbortError'){
                return;
            }
            this.#handleError(error);
        }
    }
    
    
    async #handleResponse(result){
        if(!result.ok){
            throw new Error(`Network error: ${result.status} - ${result.statusText}`);
        }
        return result;
    }
    
    #isRelativeUrl(url) {
        // Vérifie si la chaîne commence par "/", "./" ou "../", typiquement des indicateurs d'URL relative
        return url.startsWith('/') || url.startsWith('./') || url.startsWith('../');
    }
    
    #handleError(error) {
        // Arrêter le loader SweetAlert
        // Mettre à jour la fenêtre SweetAlert avec le message d'erreur
        swal.update({
            text: "Request failed !!",
            icon: "warning", // Changer l'icône pour avertir l'utilisateur
        });
        
        swal.showValidationMessage(
            `Error: ${error.message || error}`
        );
        
        swal.stopLoading();
        // Afficher le message d'erreur dans la validation
  
    }
    
    initializeAttributes(link = null) {
        // Use either data-* or Stimulus attributes based on the presence of link
        link ? this.initializeDataAtt(link) : this.initializeStimulusAtt();
    }
    
    initializeStimulusAtt(){
        // Initialize values based on data- attributes
        this.#initializeStAttValues();
    }
    
    initializeDataAtt(link){
        // Initialize values based on data- attributes
        this.#initializeDataAttValues(link);
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
    
    #initializeDataAttValues(link) {
        for (const key of Object.keys(this.constructor.values)) {
            // Convert the key from camelCase to kebab-case (e.g., idElement -> id-element)
            const dataAttr = `data-${this.#camelCaseToDash(key)}`;
            
            // Read the corresponding data-* attribute
            const dataValue = link.getAttribute(dataAttr);
            
            // If the data-* attribute exists, convert it as needed; otherwise, use the default value
            this[`${key}Value`] = dataValue !== null
                ? this.#convertDataValue(dataValue)  // Convert if necessary
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