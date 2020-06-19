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
                <a
                    @click.prevent="selectComponent('packages')"
                    :class="['nav-link', packageTabClasses ]"
                    href="#">Package</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('candies')"
                    :class="['nav-link', candyTabClasses]"
                    href="#">Candies</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('result')"
                    :class="['nav-link', resultTabClasses]"
                    href="#">Dein Combo</a>
            </li>

        </ul>
        <br><br>
        <div>
            <Spinner v-if="loading"/>

            <CardDeck v-if="currentComponent === 'events' && !loading" :entities="eclmEvents"
                      :getEntityEvent="'getLabelsEvent'"/>
            <CardDeck v-if="currentComponent === 'labels' && !loading" :entities="eclmLabels"
                      :getEntityEvent="'getPackagesEvent'"/>
            <CardDeck v-if="currentComponent === 'packages' && !loading" :entities="eclmPackages"
                      :getEntityEvent="'getCandiesEvent'"/>
            <CardDeck v-if="currentComponent === 'candies' && !loading" :entities="eclmCandies"
                      :getEntityEvent="'getResultEvent'"/>
            <Result v-if="currentComponent === 'result' && !loading" :result="eclmResult"/>
        </div>
    </div>
</template>

<script>
    import StoreApiClient from 'src/service/store-api-client.service';
    import CardDeck from "./components/CardDeck.vue";
    import {bus} from '../main.js'
    import Spinner from "./components/Spinner.vue";
    import Result from "./components/Result.vue";

    export default {
        components: {
            CardDeck,
            Spinner,
            Result
        },
        data() {
            return {
                currentComponent: '',
                loading: true,

                events: null,
                labels: null,
                packages: null,
                candies: null,

                selectedEvent: '',
                selectedLabel: '',
                selectedPackage: '',
                selectedCandy: '',
            }
        },

        computed: {
            httpClient() {
                return new StoreApiClient(window.accessKey);
            },


            //#template values
            eclmEvents() {
                return this.computeCard(this.events);
            },

            eclmLabels() {
                return this.computeCard(this.labels);
            },

            eclmPackages() {
                return this.computeCard(this.packages);
            },

            eclmCandies() {
                return this.computeCard(this.candies);
            },

            eclmResult() {
                let event = 'No event selected';
                let label = 'No label selected';
                let eclm_package = 'No package selected';
                let candy = 'No candy selected';
                if (this.selectedEvent) {
                    event = Object.values(this.events)
                        .filter((e) => e.id === this.selectedEvent)[0];
                }

                if (this.selectedLabel) {
                    label = this.labels && Object.values(this.labels)
                        .filter((e) => e.id === this.selectedLabel)[0];
                }

                if (this.selectedPackage) {
                    eclm_package = Object.values(this.packages)
                        .filter((e) => e.id === this.selectedPackage)[0];
                }

                if (this.selectedCandy) {
                    candy = Object.values(this.candies)
                        .filter((e) => e.id === this.selectedCandy)[0];
                }

                return {
                    'event': event,
                    'label': label,
                    'eclm_package': eclm_package,
                    'candy': candy,
                };
            },

            //#tabs
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
            },

            packageTabClasses() {
                let classes = ''
                if (this.currentComponent === 'packages') {
                    classes += ' active';
                }
                if (!this.selectedLabel) {
                    classes += ' disabled'
                }
                return classes;
            },

            candyTabClasses() {
                let classes = ''
                if (this.currentComponent === 'candies') {
                    classes += ' active';
                }
                if (!this.selectedPackage) {
                    classes += ' disabled'
                }
                return classes;
            },

            resultTabClasses() {
                let classes = ''
                if (this.currentComponent === 'result') {
                    classes += ' active';
                }
                if (!this.selectedCandy) {
                    classes += ' disabled'
                }
                return classes;
            },


        },

        methods: {
            componentCreated() {
                this.getEvents();

                bus.$on('getLabelsEvent', (id) => {
                    this.selectedEvent = id;
                    this.getLabels(id);
                });

                bus.$on('getPackagesEvent', (id) => {
                    this.selectedLabel = id;
                    this.getPackages();
                });

                bus.$on('getCandiesEvent', (id) => {
                    this.selectedPackage = id;
                    this.getCandies(id);
                })

                bus.$on('getResultEvent', (id) => {
                    this.selectedCandy = id;
                    this.currentComponent = 'result';
                })


            },

            selectComponent(component) {
                this.currentComponent = component;
            },

            //#api

            getEvents() {
                this.currentComponent = 'events'
                this.loading = true;
                this.httpClient.get('store-api/v{version}/eclm/get-events', (response) => {
                    this.loading = false;
                    this.events = JSON.parse(response);
                });
            },

            getLabels(id) {
                this.currentComponent = 'labels';
                this.loading = true;
                this.httpClient.get(`store-api/v{version}/eclm/get-labels/${id}`, (response) => {
                    this.loading = false;
                    this.labels = JSON.parse(response);

                    this.selectedLabel = '';
                    this.selectedPackage = '';
                    this.selectedCandy = '';
                });
            },

            getPackages() {
                this.currentComponent = 'packages'
                this.loading = true;
                this.httpClient.get('store-api/v{version}/eclm/get-packages', (response) => {
                    this.loading = false;
                    this.packages = JSON.parse(response);
                    this.selectedPackage = '';
                    this.selectedCandy = '';
                });
            },

            getCandies(id) {
                this.currentComponent = 'candies';
                this.loading = true;
                this.httpClient.get(`store-api/v{version}/eclm/get-candies/${id}`, (response) => {
                    this.loading = false;
                    this.candies = JSON.parse(response);
                    this.selectedCandy = '';
                });
            },

            //#helpers
            computeCard(entities) {
                if (!entities) {
                    return [];
                }
                return Object.values(entities).map(e => {
                    let thumbnail = e.thumbnails.filter(t => t.width === 400);
                    return {
                        'id': e.id,
                        'name': e.name,
                        'thumbnail': thumbnail[0]
                    }
                })
            },
        },

        created() {
            this.componentCreated();
        }
    }
</script>
