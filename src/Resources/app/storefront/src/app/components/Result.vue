<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <div class="result-title" v-html="header"></div>
        <br>
        <ul class="list-group">
          <li class="list-group-item"><strong>Event: </strong>{{ result.event.name }}</li>
          <li class="list-group-item"><strong>Label: </strong>{{ result.label.name }}</li>
          <li class="list-group-item"><strong>Package: </strong>
            {{ result.eclm_package.name }}
            <span class="badge badge-pill badge-info eclm-badge">
                                {{ result.eclm_package.gramm }}<i>g</i>
            </span>
          </li>

          <li class="list-group-item"><strong>Candy: </strong>{{ result.candy.name }}</li>
        </ul>

        <br>
        <QuantitySelect
            :availableStock="availableStock"
            :minimalQuantity="minimalQuantity"
            :maximalQuantity="this.result.eclm_package.maximalQuantity"
            :purchaseSteps="purchaseSteps"
        />
        <button @click.prevent="insertCart" type="button" class="btn btn-primary" style="display: inline-block;">In den
          Warenkorb
        </button>
        <span style="font-size: large">
          <strong>
          &nbsp; {{ result.eclm_package.product.currency }}{{ result.eclm_package.product.price.gross }}
          </strong> / Stück
        </span>
      </div>
      <div class="col-md-6">
        <div class="card card-result" style="max-width:200px;">
          <img :src="labelImage" class="card-img-top first" :alt="result.label.name">
        </div>

        <div class="card card-result" style="max-width:200px;">
          <img :src="packageImage" class="card-img-top second" :alt="result.eclm_package.name">

        </div>
      </div>
    </div>


  </div>
</template>

<script>
import StoreApiClient from 'src/service/store-api-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import QuantitySelect from "./QuantitySelect.vue";
import {bus} from "../../main";

export default {
  components: {QuantitySelect},
  data() {
    return {
      cartEl: null,
      selectedQuantity: 1,
      availableStock: 1
    }
  },

  props: ['result', 'header'],

  computed: {

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
    }
  },

  methods: {
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


    }
  },

  created() {
    this.availableStock = this.result.eclm_package.availableStock;

    this.cartEl = DomAccess.querySelector(document, '.header-cart');

    bus.$on('quantity-selected', (quantity) => {
      this.selectedQuantity = quantity;
    })
  }
}
</script>
