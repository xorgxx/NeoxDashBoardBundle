export default class EventHandler {
    constructor(controller) {
        this.controller = controller;
    }
    
    setupEventListeners() {
        addEventListener("turbo:before-stream-render", this.handleTurboStream.bind(this));
        addEventListener('favorite:refresh', this.handleFavoriteRefresh.bind(this));
    }
    
    removeEventListeners() {
        removeEventListener("turbo:before-stream-render", this.handleTurboStream.bind(this));
        removeEventListener('favorite:refresh', this.handleFavoriteRefresh.bind(this));
    }
    
    handleTurboStream(event) {
        const fallbackToDefaultActions = event.detail.render;
        event.detail.render = (streamElement) => {
            const idComponent = streamElement.getAttribute("data-neox-idComponent");
            const idClass = streamElement.getAttribute("data-neox-idClass");
            const action = streamElement.getAttribute("data-neox-action");
            
            if (idComponent && action) {
                console.log("xorg wants to make a call to 🦖🦖");
                this.controller.triggerComponentAction(idComponent, action, { query: idClass });
            }
            fallbackToDefaultActions(streamElement);
        };
    }
    
    handleFavoriteRefresh(event) {
        const { idComponent, idClass, action } = event.detail;
        if (idComponent && action) {
            this.controller.triggerComponentAction(idComponent, action, { query: idClass });
        } else {
            console.warn(`Invalid parameters in favorite:refresh event`, event.detail);
        }
    }
}
