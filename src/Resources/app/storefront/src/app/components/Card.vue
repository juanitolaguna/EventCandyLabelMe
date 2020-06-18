<template>
    <div class="card" :id="id" style="max-width:250px;">
        <img :src="url" class="card-img-top" :alt="title">
        <div class="card-body">
            <h5 class="card-title">{{title}}</h5>
            <a href="#" v-on:click.prevent="onSelect(id)" class="btn btn-primary">Select {{title }}</a>
        </div>
    </div>
</template>

<script>
    import {bus} from '../../main.js'
    export default {
        props: ['title', 'url', 'id'],
        methods: {
            onSelect(id) {
                bus.$emit('getLabelsEvent', id);
            }
        }
    }
</script>
