<template>
    <div>
        <h2 class="result-title">Deine Auswahl</h2>
        <button @click.prevent="insertCart" type="button" class="btn btn-primary">In den Warenkorb</button>

        <br><br>
        <ul class="list-group">
            <li class="list-group-item"><strong>Event: </strong>{{ result.event.name}}</li>
            <li class="list-group-item"><strong>Label: </strong>{{ result.label.name}}</li>
            <li class="list-group-item"><strong>Package: </strong>{{ result.eclm_package.name}}</li>
            <li class="list-group-item"><strong>Candy: </strong>{{ result.candy.name}}</li>
        </ul>

        <br><br>

        <div class="card-deck">
            <div class="card" style="max-width:250px;">

                <img :src="packageImage" class="card-img-top" :alt="result.eclm_package.name">
                <div class="card-body">
                    <h5 class="card-title">Verpackung</h5>
                </div>
            </div>
            <div class="card" style="max-width:250px;">
                <img :src="labelImage" class="card-img-top" :alt="result.label.name">
                <div class="card-body">
                    <h5 class="card-title">Label</h5>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import {bus} from "../../main";

    export default {

        props: ['result'],

        computed: {
            packageImage() {
                const thumbnail =  this.result.candy.thumbnails.filter((e) => e.width === 400)[0];
                return thumbnail.url;
            },
            labelImage() {
                const thumbnail =  this.result.label.thumbnails.filter((e) => e.width === 400)[0];
                return thumbnail.url;
            }
        },

        methods: {
            insertCart() {
                alert('Steht auf der Todo Liste!')
            }
        }
    }
</script>
