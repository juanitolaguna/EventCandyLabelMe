<template>
    <div class="eclm-container">
        <ul class="nav nav-tabs" v-if="config">
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('events')"
                    :class="['nav-link', eventTabClasses ]"
                    href="#">{{config.eventName}}</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('labels')"
                    :class="['nav-link', labelTabClasses ]"
                    href="#">{{config.labelName}}</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('candies')"
                    :class="['nav-link', candyTabClasses]"
                    href="#">{{config.candyName}}</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('packages')"
                    :class="['nav-link', packageTabClasses ]"
                    href="#">{{config.packageName}}</a>
            </li>
            <li class="nav-item">
                <a
                    @click.prevent="selectComponent('result')"
                    :class="['nav-link', resultTabClasses]"
                    href="#">{{config.comboName}}</a>
            </li>

        </ul>
        <br><br>
        <div v-if="stock">
            <Spinner v-if="loading"/>

            <transition name="slide-fade">
                <CardDeck v-if="currentComponent === 'events' && !loading"
                          :entities="eclmEvents"
                          :getEntityEvent="'getLabelsEvent'"
                          :selectedEntity="selectedEvent"
                          :header="config.eventHeader"/>
            </transition>

            <transition name="slide-fade">
                <CardDeck v-if="currentComponent === 'labels' && !loading"
                          :entities="eclmLabels"
                          :getEntityEvent="'getCandiesEvent'"
                          :selectedEntity="selectedLabel"
                          :header="config.labelHeader"/>
            </transition>

            <transition name="slide-fade">
                <CardDeck v-if="currentComponent === 'candies' && !loading"
                          :entities="eclmCandies"
                          :getEntityEvent="'getPackagesEvent'"
                          :selectedEntity="selectedCandy"
                          :header="config.candyHeader"/>
            </transition>

            <transition name="slide-fade">
                <CardDeck v-if="currentComponent === 'packages' && !loading"
                          :entities="eclmPackages"
                          :getEntityEvent="'getResultEvent'"
                          :selectedEntity="selectedPackage"
                          :header="config.packageHeader"/>
            </transition>
            <transition name="slide-fade">
                <Result
                    v-if="currentComponent === 'result'
                    && !loading"
                    :result="eclmResult"
                    :header="config.comboHeader"/>
                />
            </transition>
        </div>
        <div v-else style="padding: 5px; max-width: 50%">
            <p>Zur zeit sind keine Label Me Produkte im bestand Vorhanden, aber wir arbeiten dran.</p>
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

                stock: true,

                events: null,
                labels: null,
                packages: null,
                candies: null,

                selectedEvent: '',
                selectedLabel: '',
                selectedPackage: '',
                selectedCandy: '',

                config: null,
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
                let candy = 'No candy selected';
                let eclm_package = 'No package selected';
                if (this.selectedEvent) {
                    event = Object.values(this.events)
                        .filter((e) => e.id === this.selectedEvent)[0];
                }

                if (this.selectedLabel) {
                    label = this.labels && Object.values(this.labels)
                        .filter((e) => e.id === this.selectedLabel)[0];
                }

                if (this.selectedCandy) {
                    candy = Object.values(this.candies)
                        .filter((e) => e.id === this.selectedCandy)[0];
                }

                if (this.selectedPackage) {
                    eclm_package = Object.values(this.packages)
                        .filter((e) => e.id === this.selectedPackage)[0];
                }


                return {
                    'event': event,
                    'label': label,
                    'candy': candy,
                    'eclm_package': eclm_package,
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

            candyTabClasses() {
                let classes = ''
                if (this.currentComponent === 'candies') {
                    classes += ' active';
                }
                if (!this.selectedLabel) {
                    classes += ' disabled'
                }
                return classes;
            },

            packageTabClasses() {
                let classes = ''
                if (this.currentComponent === 'packages') {
                    classes += ' active';
                }
                if (!this.selectedCandy) {
                    classes += ' disabled'
                }
                return classes;
            },


            resultTabClasses() {
                let classes = ''
                if (this.currentComponent === 'result') {
                    classes += ' active';
                }
                if (!this.selectedPackage) {
                    classes += ' disabled';
                }
                return classes;
            },


        },

        methods: {
            componentCreated() {
                this.getConfig();
                // this.getEvents();

                bus.$on('getLabelsEvent', (id) => {
                    this.selectedEvent = id;
                    this.getLabels(id);
                });

                bus.$on('getCandiesEvent', (id) => {
                    this.selectedLabel = id;
                    this.getCandies();
                })

                bus.$on('getPackagesEvent', (id) => {
                    this.selectedCandy = id;
                    this.getPackages(id);
                });

                bus.$on('getResultEvent', (id) => {
                    this.selectedPackage = id;
                    this.currentComponent = 'result';
                })

                this.getStock();


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
                    this.selectedCandy = '';
                    this.selectedPackage = '';
                });
            },

            getCandies() {
                this.currentComponent = 'candies';
                this.loading = true;
                this.httpClient.get(`store-api/v{version}/eclm/get-candies`, (response) => {
                    this.loading = false;
                    this.candies = JSON.parse(response);
                    this.selectedCandy = '';
                    this.selectedPackage = '';
                });
            },

            getPackages(id) {
                this.currentComponent = 'packages'
                this.loading = true;
                this.httpClient.get(`store-api/v{version}/eclm/get-packages/${id}`, (response) => {
                    this.loading = false;
                    this.packages = JSON.parse(response);
                    this.selectedPackage = '';
                });
            },

            getConfig() {
                this.httpClient.get(`store-api/v{version}/eclm/get-config`, (response) => {
                    this.loading = false;
                    this.config = JSON.parse(response);
                    this.getEvents();
                });
            },

            getStock() {
                this.httpClient.get(`store-api/v{version}/eclm/get-candies`, (response) => {
                    const res = JSON.parse(response);
                    if (!res) {
                        this.stock = false;
                    }
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
                        'thumbnail': thumbnail[0],
                        'gramm': e.gramm ? e.gramm : undefined,
                        'product': e.product ? e.product : undefined
                    }
                })
            },
        },

        created() {
            this.componentCreated();
        }
    }
</script>
