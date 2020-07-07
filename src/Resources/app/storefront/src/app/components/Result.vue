<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="result-title" v-html="header"></div>
                <button @click.prevent="insertCart" type="button" class="btn btn-primary">In den Warenkorb</button>
                <br><br>

                <ul class="list-group">
                    <li class="list-group-item"><strong>Event: </strong>{{ result.event.name}}</li>
                    <li class="list-group-item"><strong>Label: </strong>{{ result.label.name}}</li>
                    <li class="list-group-item"><strong>Package: </strong>{{ result.eclm_package.name}}</li>
                    <li class="list-group-item"><strong>Candy: </strong>{{ result.candy.name}}</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="card card-result" style="max-width:200px;">
                    <img :src="labelImage" class="card-img-top" :alt="result.label.name">
                </div>

                <div class="card card-result" style="max-width:200px;">
                    <img :src="packageImage" class="card-img-top" :alt="result.eclm_package.name">

                </div>
            </div>
        </div>


    </div>
</template>

<script>
    import {bus} from "../../main";

    export default {

        props: ['result', 'header'],

        computed: {
            packageImage() {
                const thumbnail = this.result.eclm_package.thumbnails.filter((e) => e.width === 400)[0];
                return thumbnail.url;
            },
            labelImage() {
                const thumbnail = this.result.label.thumbnails.filter((e) => e.width === 400)[0];
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
