<template>
  <div
      class="modal fade"
      v-bind:class="{show: modalActive, 'eclm-show-front-modal': modalActive}"
      id="productDataModal"
      tabindex="-1" role="dialog"
      aria-labelledby="productDataModalTitle"
      aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button v-on:click.prevent="onOk" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div v-for="infoBlock in data">
            <div v-html="infoBlock"></div>
            <hr>
          </div>
        </div>
        <div class="modal-footer" style="justify-content: center">
          <div>
            <!-- show only on mobile -->
            <button v-on:click.prevent="onOk" style="min-width: min-content" type="button"
                    class="btn btn-primary d-block d-sm-none">
              <span>Schließen</span>
            </button>
          </div>

          <button v-on:click.prevent="onOk" style="min-width: min-content" type="button"
                  class="btn btn-primary d-none d-sm-block">
            <span >Schließen</span>
          </button>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {bus} from "../../main";

export default {
  props: ['modalActive', 'data'],


  methods: {
    onOk() {
        this.$emit('close-product-modal');
    }
  }
}
</script>
