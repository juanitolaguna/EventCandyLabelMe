<template>
  <div
      class="modal fade"
      v-bind:class="{show: showModal, 'eclm-show-front-modal': showModal}"
      id="exampleModalCenter"
      tabindex="-1" role="dialog"
      aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button v-on:click.prevent="onOk" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" v-html="introModal">
        </div>
        <div class="modal-footer" style="justify-content: space-between;  padding-right: 3em; padding-left: 3em;">
          <div>
            <div v-if="introModalCheckbox">
              <input type="checkbox" id="checkbox" v-model="checked">
              <label for="checkbox">{{ introModalCheckbox }}</label>
            </div>

            <!-- show only on mobile -->
            <button v-on:click.prevent="onOk" style="min-width: min-content" type="button"
                    class="btn btn-primary d-block d-sm-none">
              <span v-if="introModalCTA">{{ introModalCTA }}</span>
              <span v-else>Ok</span>
            </button>
          </div>

          <button v-on:click.prevent="onOk" style="min-width: min-content" type="button"
                  class="btn btn-primary d-none d-sm-block">
            <span v-if="introModalCTA">{{ introModalCTA }}</span>
            <span v-else>Ok</span>
          </button>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {bus} from "../../main";


export default {
  props: ['introModal', 'introModalActive', 'introModalCTA', 'introModalCheckbox'],

  data() {
    return {
      checked: false
    }
  },

  methods: {
    onOk() {
      bus.$emit('close-modal');
      if (this.checked) {
        sessionStorage.labelMeModalClicked = true;
      }
    }
  },

  computed: {
    showModal() {
      const storageNull = sessionStorage['labelMeModalClicked'] === undefined;
      return storageNull && this.introModalActive;
      // return this.introModalActive;
    }
  }


}
</script>
