<template>
  <div
      class="modal fade"
       v-bind:class="{show: showModal, 'eclm-show-front-modal': showModal}"
       id="exampleModalCenter"
       tabindex="-1" role="dialog"
       aria-labelledby="exampleModalCenterTitle"
       aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body" v-html="introModal">

        </div>
        <div class="modal-footer">
          <button v-on:click.prevent="onOk" type="button" class="btn btn-primary">
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
  props: ['introModal', 'introModalActive', 'introModalCTA'],

  methods: {
      onOk(){
        bus.$emit('close-modal');
        localStorage.labelMeModalClicked = true;
      }
  },

  computed: {
    showModal() {
      const storageNull = localStorage['labelMeModalClicked'] === undefined;
      // return storageNull && this.introModalActive;
      return this.introModalActive;
    }
  }


}
</script>
