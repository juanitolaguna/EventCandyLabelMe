import Plugin from 'src/plugin-system/plugin.class';

export default class EventCandyLabelMe extends Plugin {
    init() {
        if (window.location.pathname === '/label-me') {
            if (this.el.attributes.href.value === '/label-me') {
                this.el.attributes.class.value += ' activdoe'
            }
        }
    }
}