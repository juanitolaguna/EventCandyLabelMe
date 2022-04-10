<template>
  <div ref="top" class="eclm-container">
    <ul class="nav nav-tabs" v-if="config">
      <li class="nav-item">
        <a
            @click.prevent="selectComponent('events')"
            :class="['nav-link', eventTabClasses ]"
            href="#">{{ config.eventName }}</a>
      </li>
      <li class="nav-item">
        <a
            @click.prevent="selectComponent('labels')"
            :class="['nav-link', labelTabClasses ]"
            href="#">{{ config.labelName }}</a>
      </li>
      <li class="nav-item">
        <a
            @click.prevent="selectComponent('candies')"
            :class="['nav-link', candyTabClasses]"
            href="#">{{ config.candyName }}</a>
      </li>
      <li class="nav-item">
        <a
            @click.prevent="selectComponent('packages')"
            :class="['nav-link', packageTabClasses ]"
            href="#">{{ config.packageName }}</a>
      </li>
      <li class="nav-item">
        <a
            @click.prevent="selectComponent('result')"
            :class="['nav-link', resultTabClasses]"
            href="#">{{ config.comboName }}</a>
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
                  :config="config"
                  :component="currentComponent"
                  :header="config.eventHeader"/>
      </transition>

      <transition name="slide-fade">
        <CardDeck v-if="currentComponent === 'labels' && !loading"
                  :entities="eclmLabels"
                  :getEntityEvent="'getCandiesEvent'"
                  :selectedEntity="selectedLabel"
                  :config="config"
                  :component="currentComponent"
                  :header="config.labelHeader"/>
      </transition>

      <transition name="slide-fade">
        <CardDeck v-if="currentComponent === 'candies' && !loading"
                  :entities="eclmCandies"
                  :getEntityEvent="'getPackagesEvent'"
                  :selectedEntity="selectedCandy"
                  :config="config"
                  :component="currentComponent"
                  :header="config.candyHeader"/>
      </transition>

      <transition name="slide-fade">
        <CardDeck v-if="currentComponent === 'packages' && !loading"
                  :entities="eclmPackages"
                  :getEntityEvent="'getResultEvent'"
                  :selectedEntity="selectedPackage"
                  :config="config"
                  :component="currentComponent"
                  :header="config.packageHeader"/>
      </transition>
      <transition name="slide-fade">
        <Result
            v-if="currentComponent === 'result'
                    && !loading"
            :result="eclmResult"
            :header="config.comboHeader"
            :config="config"
            :component="currentComponent"
        />
        />
      </transition>
    </div>
    <div v-else style="padding: 5px; max-width: 50%">
      <p>Zur zeit sind keine Label Me Produkte im bestand Vorhanden, aber wir arbeiten dran.</p>
    </div>
    <Modal :introModal="config.introModal"
           :introModalActive="config.introModalActive"
           :introModalCTA="config.introModalCTA"
           :introModalCheckbox="config.introModalCheckbox"
    />
    <img v-if="config.placeholderImageUrl" :src="config.placeholderImageUrl" style="display: none">
  </div>
</template>

<script>
import StoreApiClient from 'src/service/store-api-client.service';
import CardDeck from "./components/CardDeck.vue";
import {bus} from '../main.js'
import Spinner from "./components/Spinner.vue";
import Result from "./components/Result.vue";
import Modal from "./components/Modal.vue"

export default {
  components: {
    CardDeck,
    Spinner,
    Result,
    Modal,
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
      selectedCandyData: '',

      config: [],
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
    async componentCreated() {
      await this.getConfig();
      // this.getEvents();

      bus.$on('getLabelsEvent', (id) => {
        this.$refs.top.scrollIntoView();
        this.selectedEvent = id;
        this.getLabels(id);
      });

      bus.$on('getCandiesEvent', (id) => {
        this.$refs.top.scrollIntoView();
        this.selectedLabel = id;
        this.getCandies();
      })

      bus.$on('getPackagesEvent', (id, options) => {
        this.$refs.top.scrollIntoView();
        this.selectedCandy = id;
        this.selectedCandyData = options;
        this.getPackages(id);
      });

      bus.$on('getResultEvent', (id, options) => {
        this.$refs.top.scrollIntoView();
        this.selectedPackage = id;
        this.selectedPackageData = options;
        this.currentComponent = 'result';
      })

      bus.$on('close-modal', () => {
        this.config.introModalActive = false;
      });

      bus.$on('showNotAvailableBadge', (id, entityType) => {
        this.showNotAvailableBadge(id, entityType)
      });

      await this.getStock();
      await this.getEventFromParameter();
    },


    showNotAvailableBadge(id, entityType) {
      const delay = this.config.showNotAvailableBadgeDelay ? this.config.showNotAvailableBadgeDelay : 2000;
      this[entityType][id]['showNotAvailableBadge'] = true;
      setTimeout(() => {
        this[entityType][id]['showNotAvailableBadge'] = false;
      }, delay);
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

    getLabels(id, callback = null) {
      this.currentComponent = 'labels';
      this.loading = true;
      this.httpClient.get(`store-api/v{version}/eclm/get-labels/${id}`, (response) => {
        this.loading = false;
        this.labels = JSON.parse(response);
        this.selectedLabel = '';
        this.selectedCandy = '';
        this.selectedCandyData = '';
        this.selectedPackage = '';
        this.selectedPackageData = '';
        if (callback) {
          callback();
        }
      });
    },

    getCandies() {
      this.currentComponent = 'candies';
      this.loading = true;
      this.httpClient.get(`store-api/v{version}/eclm/get-candies`, (response) => {
        this.loading = false;
        this.candies = JSON.parse(response);
        this.selectedCandy = '';
        this.selectedCandyData = '';
        this.selectedPackage = '';
        this.selectedPackageData = '';
      });
    },

    getPackages(id) {
      this.currentComponent = 'packages'
      this.loading = true;
      this.httpClient.get(`store-api/v{version}/eclm/get-packages/${id}`, (response) => {
        this.loading = false;
        this.packages = JSON.parse(response);
        this.selectedPackage = '';
        this.selectedPackageData = '';
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

    async getEventFromParameter() {
      const params = new URLSearchParams(window.location.search);
      const id = params.get('event');
      if (id) {
        this.$refs.top.scrollIntoView();
        this.selectedEvent = id;
        let cb = () => this.selectComponent('labels');
        this.getLabels(id, cb);
      }
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
          'infoBadge': e.infoBadge ? e.infoBadge: undefined,
          'infoBadgeColor': e.infoBadgeColor ? e.infoBadgeColor: undefined,
          'alternativeName': e.alternativeName ? e.alternativeName : undefined,
          'productData': e.productData ? e.productData : undefined,
          'thumbnail': thumbnail[0],
          'gramm': e.gramm ? e.gramm : undefined,
          'product': e.product ? e.product : undefined,
          'availableStock': e.availableStock ? e.availableStock : undefined,
          'purchaseSteps': e.purchaseSteps ? e.purchaseSteps : 1,
          'minimalQuantity': e.minimalQuantity ? e.minimalQuantity : undefined,
          'maximalQuantity': e.maximalQuantity ? e.maximalQuantity : undefined,
          'cssSize': e.cssSize ? e.cssSize : undefined,
          'packageType': e.packageType ? e.packageType : undefined,
          'notAvailable': e.notAvailable ? e.notAvailable : undefined,
          'showNotAvailableBadge': e.showNotAvailableBadge,
          'selectedCandyData': this.selectedCandyData ? this.selectedCandyData : undefined
        }
      })
    },
  },

  created() {
    this.componentCreated();
  },

  mounted() {
    setTimeout(() => {
      this.$refs.top.scrollIntoView({behavior: "smooth"});
    }, 1000);
  }
}
</script>
