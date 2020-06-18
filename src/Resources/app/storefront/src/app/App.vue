<template>
    <div class="eclm-container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('events')"
                    :class="['nav-link', eventTabClasses ]"
                    href="#">Event</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('labels')"
                    :class="['nav-link', labelTabClasses ]"
                    href="#">Label</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">{{ selectedEvent }}</a>
            </li>
        </ul>
        <br><br>
        <CardDeck v-if="currentComponent === 'events'" :entity="eclmEvents" />
        <CardDeck v-if="currentComponent === 'labels'" :entity="eclmLabels" />
    </div>
</template>

<script>
    import StoreApiClient from 'src/service/store-api-client.service';
    import CardDeck from "./components/CardDeck.vue";
    import {bus} from '../main.js'

    export default {
        components: {
            CardDeck
        },
        data() {
            return {
                events: null,
                labels: null,
                currentComponent: 'events',
                selectedEvent: '',
                selectLabel: '',
                selectedPackage: '',
                selectedCandy:''
            }
        },

        computed: {
            httpClient() {
                return new StoreApiClient(window.accessKey);
            },

            eclmEvents() {
                if (!this.events) {
                    return [];
                }
                return Object.values(this.events).map(e => {
                    let thumbnail = e.thumbnails.filter(t => t.width === 400);
                    return {
                        'id': e.id,
                        'name': e.name,
                        'thumbnail': thumbnail[0]
                    }
                })
            },

            eclmLabels() {
                if (!this.labels) {
                    return [];
                }
                return Object.values(this.labels).map(e => {
                    let thumbnail = e.thumbnails.filter(t => t.width === 400);
                    return {
                        'id': e.id,
                        'name': e.name,
                        'thumbnail': thumbnail[0]
                    }
                })
            },

            //tabs
            eventTabClasses() {
                let classes = ''
                if (this.currentComponent === 'events') {
                    classes += ' active';
                }
                return classes;
            },

            labelTabClasses() {
                let classes = ''
                if (this.currentComponent === 'labels') {
                    classes += ' active';
                }
                if (!this.selectedEvent) {
                    classes += ' disabled'
                }
                return classes;
            }

        },

        methods: {
            componentCreated() {
                this.getEvents();
                bus.$on('getLabelsEvent', (id) => {
                    this.selectedEvent = id;
                    this.getLabels(id);
                })
            },

            selectComponent(component) {
                this.currentComponent = component;
            },

            getEvents() {
                this.httpClient.get('store-api/v{version}/eclm/get-events', (response) => {
                    this.events = JSON.parse(response);
                    this.labels = '';
                });
            },

            getLabels(id) {
                this.httpClient.get(`store-api/v{version}/eclm/get-labels/${id}`, (response) => {
                    this.labels = JSON.parse(response);
                    this.currentComponent = 'labels'
                });
            }
        },
        created() {
            this.componentCreated();
        },
    }
</script>
