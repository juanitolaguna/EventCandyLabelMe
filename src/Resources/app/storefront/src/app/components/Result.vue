<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <div class="result-title" v-html="header"></div>
        <br>
        <ul class="list-group">
          <li class="list-group-item"><strong>{{ config.eventName }}: </strong>{{ result.event.name }}</li>
          <li class="list-group-item"><strong>{{ config.labelName }}: </strong>{{ result.label.name }}</li>
          <li class="list-group-item"><strong>{{ config.candyName }}: </strong>{{ result.candy.name }}</li>
          <li class="list-group-item"><strong>{{ config.packageName }}: </strong>
            {{ result.eclm_package.name }}
            <span class="badge badge-pill badge-info eclm-badge" v-if="result.eclm_package.product.purchaseUnit">
                                {{ result.eclm_package.product.purchaseUnit }}<i>g</i>
            </span>
          </li>
          <li v-if="productDataAvailable" class="list-group-item">
            <template class="product-info">
              <button v-on:click.stop="showProductModal" type="button" :class="[config.pdButtonTypeResult]">
                {{ config.pdButtonText }}
              </button>
            </template>
          </li>
        </ul>


        <div v-if="snippets.cancelPolicy" v-html="snippets.cancelPolicy" class="cancel-policy">
        </div>

        <a v-if="result.eclm_package.product.dataSheetUrl && config.utilsPlugin.turnOnDataSheet"
           :href="result.eclm_package.product.dataSheetUrl" target="_blank">
          <div v-bind:style="{backgroundColor: config.utilsPlugin.badgeColor}"
               class="badge badge-pill badge-secondary product-data-sheet"
          >
            {{ config.translations.dataSheet }}
            <span
                v-if="config.translations.dataSheetTooltip !== ''"
                class="tooltiptext"
                v-html="config.translations.dataSheetTooltip"/>
          </div>
        </a>


        <br>
        <div style="display: flex; margin-bottom: 2em; z-index: 1">
          <div style="display: inline-block; margin-right: 1em;">
            <QuantitySelect
                :availableStock="availableStock"
                :minimalQuantity="minimalQuantity"
                :maximalQuantity="this.result.eclm_package.maximalQuantity"
                :purchaseSteps="purchaseSteps"
                style="margin-bottom: 1em;"
            />
            <button @click.prevent="insertCart" type="button" class="btn btn-primary" style="display: inline-block; ">
              In den Warenkorb
            </button>
          </div>

          <div style="font-size: large; display: inline-block;">
            <strong>
              {{ result.eclm_package.product.currency }}{{ result.eclm_package.product.price.gross }}
            </strong> / Stück
            <p class="product-price-unit" v-if="referenceUnitPriceAvailable">
              <span class="price-unit-reference">
                {{ product.currency }}{{ referenceUnitPrice }} / {{ product.referenceUnit }} {{ product.unitName }}
              </span>
            </p>
          </div>
        </div>

      </div>

      <div class="col-md-6" :class="[type + '-type']" @click="toggleAnimation" style="z-index: 0">

        <div class="arrow-image" :class="[type + '-arrow-image']" v-if="config.arrowUrl && config.arrowText">
          <h5>{{ config.arrowText }}</h5>
          <img :src="config.arrowUrl" alt="arrow">
        </div>

        <div class="card card-result">
          <img ref="imageFirst" :src="labelImage"
               class="card-img-top first"
               :alt="result.label.name"
          >
        </div>

        <div class="card card-result">
          <img :src="packageImage" class="card-img-top second" :alt="result.eclm_package.name">

        </div>
      </div>
    </div>

    <ProductDataModal
        :modalActive="modalActive"
        :data="modalData"
        @close-product-modal="onModalClose"
    />

  </div>
</template>

<script>
import StoreApiClient from 'src/service/store-api-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import QuantitySelect from "./QuantitySelect.vue";
import ProductDataModal from "./ProductDataModal.vue";
import {bus} from "../../main";

export default {
  components: {QuantitySelect, ProductDataModal},
  data() {
    return {
      cartEl: null,
      selectedQuantity: this.result.eclm_package.minimalQuantity,
      availableStock: 1,
      modalActive: false,
      modalData: []
    }
  },

  props: ['result', 'header', 'config'],

  computed: {

    productDataAvailable() {
      const candyData = this.result.candy.productData;
      const packageData = this.result.eclm_package.productData;
      return candyData || packageData;
    },

    product() {
      return this.result.eclm_package.product;
    },

    referenceUnitPriceAvailable() {
      const prod = this.result.eclm_package.product;
      return prod.unitName && prod.purchaseUnit && prod.referenceUnit;
    },

    referenceUnitPrice() {
      const product = this.result.eclm_package.product;
      return ((product.price.gross / product.purchaseUnit) * product.referenceUnit).toFixed(2);
    },

    minimalQuantity() {
      return this.result.eclm_package.minimalQuantity ? this.result.eclm_package.minimalQuantity : 1;
    },

    purchaseSteps() {
      return this.result.eclm_package.purchaseSteps ? this.result.eclm_package.purchaseSteps : 1;
    },

    packageImage() {
      const thumbnail = this.result.eclm_package.thumbnails.filter((e) => e.width === 400)[0];
      return thumbnail.url;
    },
    labelImage() {
      const thumbnail = this.result.label.thumbnails.filter((e) => e.width === 400)[0];
      return thumbnail.url;
    },

    httpClient() {
      return new StoreApiClient(window.accessKey);
    },
    pluginManager() {
      return window.PluginManager
    },

    type() {
      return this.result.eclm_package.packageType;
    },

    snippets() {
      return window.Snippets;
    }
  },

  methods: {

    showProductModal() {
      const candyData = this.result.candy.productData;
      const packageData = this.result.eclm_package.productData;

      this.modalData = [];
      this.modalData.push(packageData);
      this.modalData.push(candyData);
      this.modalActive = true;
    },

    onModalClose() {
      this.modalActive = false;
      this.modalData = [];
    },

    insertCart() {
      // this.httpClient.getBasicHeaders();
      const thumbnail = this.result.eclm_package.thumbnails.filter((e) => e.width === 400)[0];
      const result = this.result;
      result.eclm_package.thumbnail = thumbnail;
      result['selectedQuantity'] = this.selectedQuantity;
      //remove unused data
      // delete result.eclm_package.thumbnails;

      return this.httpClient.post(
          'store-api/v{version}/eclm/add-line-item',
          JSON.stringify(result),
          this.onPost);
    },

    onPost(res) {
      // console.log(res)
      // const cartWidgetEl = DomAccess.querySelector(this.cartEl, '[data-cart-widget]');
      // const cartWidgetInstance = this.pluginManager.getPluginInstanceFromElement(cartWidgetEl, 'CartWidget');
      // cartWidgetInstance.fetch();
      const offCanvasCartEl = DomAccess.querySelector(document, '[data-offcanvas-cart]');
      const offCanvasCartInstance = this.pluginManager.getPluginInstanceFromElement(offCanvasCartEl, 'OffCanvasCart');
      offCanvasCartInstance.openOffCanvas(window.router['frontend.cart.offcanvas'], false);
    },

    toggleAnimation() {
      if (this.type === 'bucket' || this.type === 'globy') {
        this.$refs.imageFirst.classList.add(this.type + "-js-animation");

        setTimeout(() => {
          this.$refs.imageFirst.classList.remove(this.type + "-js-animation");
        }, 6500);
      }
    },

    addRefClass() {
      if (this.type === 'bag' || this.type === 'handypack') {
        this.$refs.imageFirst.classList.add(this.type + "-js-animation");
      }

      if (this.type === 'bucket' || this.type === 'globy') {
        setTimeout(() => {
          this.$refs.imageFirst.classList.add(this.type + "-js-animation");
        }, 3000);

        setTimeout(() => {
          this.$refs.imageFirst.classList.remove(this.type + "-js-animation");
        }, 9500);
      }
    }
  },

  created() {
    this.availableStock = this.result.eclm_package.availableStock;

    this.cartEl = DomAccess.querySelector(document, '.header-cart');

    bus.$on('quantity-selected', (quantity) => {
      this.selectedQuantity = quantity;
    })
  },

  mounted() {
    this.addRefClass()
  }
}
</script>
