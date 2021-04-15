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
        <div id="printContent" class="modal-body">
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
            <span>Schließen</span>
          </button>

          <button v-on:click.prevent="onPrint" style="min-width: min-content" type="button"
                  class="btn btn-primary">
            <span>Drucken</span>
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
    },

    onPrint() {

      const styles = `
        body {
          padding: 3rem;
        }
        .noprint {
          padding: 5px 20px;
          float: right;
          background-color: #FA9B00;
          color: white;
          border-radius: 6px;
          border: none;
          font-size: 1.2em;
          font-weight: 600;
          cursor: pointer;
        }
        @media print {
          .noprint {
            display: none !important;
          }
        }`;


      const printContent = document.getElementById('printContent');

      const w = window.open('', '_blank', 'height=800,width=600');
      w.document.write(printContent.innerHTML);

      // add styles
      const styleSheet = w.document.createElement('style');
      styleSheet.innerHTML = styles;
      w.document.head.appendChild(styleSheet);

      // add button
      const button = document.createElement("div");
      button.innerHTML = `<button class="noprint" type="button" onclick="window.print(); return false;">Drucken</button><br><br>`;
      w.document.body.prepend(button);
      w.document.close();
      w.focus();
    }
  }
}
</script>
