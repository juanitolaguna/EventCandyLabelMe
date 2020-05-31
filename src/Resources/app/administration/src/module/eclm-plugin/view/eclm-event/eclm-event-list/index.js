import template from './eclm-event-list.html.twig';
import './eclm-event-list.scss';

const {Criteria} = Shopware.Data;

Shopware.Component.register('eclm-event-list', {
    template: template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            value: [],
            mediaItem: {id: '1'},
            candies: {}
        }
    },

    computed: {
        options() {
            return [
                {value: 'uuid1', label: 'Portia Jobson'},
                {value: 'uuid2', label: 'Baxy Eardley'},
                {value: 'uuid3', label: 'Arturo Staker'},
                {value: 'uuid4', label: 'Dalston Top'},
                {value: 'uuid5', label: 'Neddy Jensen'}
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
    },

    created() {
        this.repository = this.repositoryFactory.create('eclm_candy');

        this.repository
            .search(new Criteria(), Shopware.Context.api)
            .then((result) => {
                console.log(result.first());
                this.candies = result;
            })
    }


});
