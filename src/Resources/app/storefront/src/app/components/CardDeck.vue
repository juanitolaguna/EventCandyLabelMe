<template>
    <div class="card-deck">
        <div v-for="entity in entities">
            <div class="card" id="e.id" style="max-width:250px;">
                <img :src="entity.thumbnail.url" class="card-img-top" :alt="entity.name">
                <div class="card-body">
                    <h5 class="card-title">{{ entity.name }}</h5>
                    <a href="#" v-on:click.prevent="onSelect(entity.id)" class="btn btn-primary">Select {{entity.name }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {bus} from "../../main";

    export default {

        props: ['entities', 'getEntityEvent'],

        methods: {
            onSelect(id) {
                bus.$emit(this.getEntityEvent, id);
            }
        }
    }
</script>
