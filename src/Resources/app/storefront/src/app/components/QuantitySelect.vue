<template>
  <div  style="display: inline-block;">
    <select class="custom-select" @change="onChange($event)">
      <option v-for="number in (numbers)">
          {{ number }}
      </option>
    </select>
  </div>
</template>

<script>
import {bus} from "../../main";


export default {
  props: ['availableStock', 'purchaseSteps', 'minimalQuantity', 'maximalQuantity'],

  methods: {
    onChange(event) {
      bus.$emit('quantity-selected', event.target.value );
    }
  },

  computed: {
    numbers() {
      let maxQuantity = 1
      if (this.maximalQuantity !== null) {
        maxQuantity = Math.min(this.availableStock, this.maximalQuantity);
      } else {
        maxQuantity = this.availableStock;
      }

      let minQuantity = this.minimalQuantity;

      let numbers = [];

      let counter = this.purchaseSteps;
      for (let num = minQuantity; num <= maxQuantity; num++ ) {
        if (counter === this.purchaseSteps) {
          numbers.push(num);
          counter = 1;
        } else {
          counter++
        }
      }
      return numbers;
    },



  }
}
</script>
