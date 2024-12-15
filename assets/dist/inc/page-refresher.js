export default class PageRefresher {
    constructor(controller, intervalInMinutes) {
        this.controller = controller;
        this.intervalInMinutes = intervalInMinutes || 30; // Valeur par défaut à 30 minutes
        this.timeout = null;
    }
    
    start() {
        const intervalInMilliseconds = this.intervalInMinutes * 60 * 1000;
        this.timeout = setTimeout(() => {
            this.refreshPage();
            this.logRefreshTime();
        }, intervalInMilliseconds);
    }
    
    stop() {
        if (this.timeout) {
            clearTimeout(this.timeout); // Annule le timeout
        }
    }
    
    refreshPage() {
        window.location.reload();
    }
    
    logRefreshTime() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString();
        console.log(`Page refreshed at: ${formattedTime}`);
    }
}
