export default class ClickCounter {
    constructor(controller) {
        this.controller = controller;
        this.count = 0;
    }
    
    reset() {
        this.count = 0;
        this.updateDisplay();
    }
    
    increment() {
        this.count++;
    }
    
    updateDisplay() {
        if (this.controller.hasCountTarget) {
            this.controller.countTarget.textContent = this.count;
        }
        console.info(`click 🦖🦖 ${this.count}`)
    }
    
    sendClickToServer(jsonData) {
        console.info(jsonData.token)
        fetch(jsonData.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            if (!response.ok) {
                console.error('Failed to record click');
            }
        });
    }
}
