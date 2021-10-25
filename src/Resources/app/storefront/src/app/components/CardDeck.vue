<template>
  <div>
    <div class="result-title" v-html="header"></div>
    <br><br>
    <div class="card-deck" v-if="entities.length">

      <div v-for="entity in entities">
        <div v-on:click.prevent="onSelect(entity.id, entity.productData, entity.notAvailable)"
             :class="['card eclm-card', isSelected(entity.id)]"
             :id="entity.id"
        >

          <div :class="['card-image-top-wrapper', notAvailable(entity)]" style="width: inherit">
            <v-lazy-image :src="entity.thumbnail.url" class="card-img-top"
                          :srcPlaceholder="config.placeholderImageUrl"
                          :alt="entity.name"
                          :style="{width: entity.cssSize + '%'}"/>
          </div>

          <div v-if="entity.notAvailable" :class="['not-available-text']">
            {{ snippet.notAvailable }}
          </div>

          <div v-if="entity.notAvailable" :class="['not-available-badge', showBadge(entity)]">
            <div class="inner">
              {{ snippet.notAvailableMessage }}
            </div>
          </div>

          <div class="card-body">
            <h5 class="card-title card-title-label-me"
                :style="{paddingBottom: '0px', color: config.firstLineEventCSS}">
              {{ entity.name }}
            </h5>

            <h5 v-if="entity.alternativeName" class="card-title card-title-label-me"
                :style="{paddingBottom: '0px', color: config.secondLineEventCSS}">
              {{ entity.alternativeName }}
            </h5>
            <div class="eclm-badges-container">
              <template v-if="entity.gramm">
                            <span class="badge badge-pill badge-info eclm-badge">
                                {{ entity.gramm }}<i>g</i>
                            </span>
              </template>
              <template v-if="entity.product">
                <span class="badge badge-pill badge-primary">
                  {{ entity.product.currency }}{{ entity.product.price.gross }}
                </span>
              </template>

              <template v-if="entity.productData" class="product-info">
                <span v-on:click.stop="showProductModal(entity.productData, entity.selectedCandyData)"
                      :class="[config.pdButtonType]"
                      style="cursor: pointer;">
                  {{ config.pdButtonText }}
                </span>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else style="padding: 5px; max-width: 50%">
      <p>Zur zeit sind keine Label Me Produkte im bestand Vorhanden, aber wir arbeiten dran.</p>
    </div>
    <ProductDataModal
        :modalActive="modalActive"
        :data="modalData"
        @close-product-modal="closeModal"
    />
  </div>
</template>

<script>
import {bus} from "../../main";
import TestComponent from "./TestComponent.vue";
import ProductDataModal from "./ProductDataModal.vue";
import VLazyImage from "v-lazy-image";


export default {
  components: {
    TestComponent,
    ProductDataModal,
    VLazyImage
  },

  props: ['entities', 'getEntityEvent', 'selectedEntity', 'phrase', 'header', 'config', 'component'],

  data() {
    return {
      modalActive: false,
      modalData: []
    }
  },

  computed: {
    snippet() {
      switch (this.component) {
        case 'events':
          return {
            'notAvailable': window.Snippets.eventNotAvailable,
            'notAvailableMessage': window.Snippets.eventNotAvailableMessage
          }
        case 'candies':
          return {
            'notAvailable': window.Snippets.candyNotAvailable,
            'notAvailableMessage': window.Snippets.candyNotAvailableMessage
          }
        default:
          return false;
      }
    }
  },

  methods: {
    onSelect(id, options, notAvailable) {
      if (notAvailable) {
        return bus.$emit('showNotAvailableBadge', id, this.component);
      }
      bus.$emit(this.getEntityEvent, id, options);
    },

    showBadge(entity) {
      let classes = '';
      if (entity['showNotAvailableBadge']) {
        classes += ' show';
      }
      return classes;
    },

    notAvailable(entity) {
      let classes = '';
      if (entity.notAvailable) {
        classes += ' not-available';
      }
      return classes;
    },

    isSelected(id) {
      let classes = ''
      if (this.selectedEntity === id) {
        classes += ' card-selected';
      }
      return classes;
    },

    showProductModal(productData, candyData) {
      this.modalData = [];
      this.modalData.push(productData);
      if (candyData && this.getEntityEvent !== 'getPackagesEvent') {
        this.modalData.push(candyData);
      }
      this.modalActive = true;
    },

    closeModal() {
      this.modalActive = false;
      this.modalData = [];
    }
  }

}
</script>
