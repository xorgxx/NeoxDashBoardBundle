export default class SearchBrowser {
    static targets = ["browser"];  // Remplacer query par browser
    
    constructor(controller){
        this.controller = controller;
        
        // Définir plusieurs moteurs de recherche
        this.searchEngines = {
            google      : "https://www.google.com/search?q=",
            bing        : "https://www.bing.com/search?q=",
            duckduckgo  : "https://duckduckgo.com/?q=",
            yahoo       : "https://search.yahoo.com/search?p="
        };
        
        this.defaultEngine = "google"; // Moteur par défaut
    }
    
    search(event) {
        
        const input     = event.currentTarget.querySelector('[data-main-query="element"]');
        const query     = input.value.trim();// Récupère la valeur de l'input via data-target
        const browser   = event.currentTarget.getAttribute('data-main-browser').toLowerCase();  // Récupère l'attribut 'data-main-browser' du formulaire
        
        
        // Vérifie que la requête et le moteur de recherche sont valides
        if (query && this.searchEngines[browser]) {
            const searchUrl = this.searchEngines[browser] + encodeURIComponent(query);
            window.open(searchUrl, "_blank");  // Ouvre la recherche dans un nouvel onglet
        } else {
            console.warn("Invalid search query or search engine configuration.");
        }
    }
}
