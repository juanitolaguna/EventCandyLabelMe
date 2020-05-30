import template from './eclm-event-list.html.twig';
import './eclm-event-list.scss';

Shopware.Component.register('eclm-event-list', {
    template: template,

    data() {
        return {
            value: []
        }
    },

    computed: {
        options() {
            return [
                { value:'uuid1', label:'Portia Jobson' },
                { value:'uuid2', label:'Baxy Eardley' },
                { value:'uuid3', label:'Arturo Staker' },
                { value:'uuid4', label:'Dalston Top' },
                { value:'uuid5', label:'Neddy Jensen' }
            ]
        }

    },

    methods: {
        setValue(value) {
            console.log(value);
            this.value = value;
        },

        alertSelection() {
            alert(this.value);
        },

        testAlert(input) {
            alert(input);
        }
    }


});
