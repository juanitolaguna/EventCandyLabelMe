<template>
    <div>
        <div class="result-title" v-html="header"></div>
        <br><br>
        <div class="card-deck" v-if="entities.length">
            <div v-for="entity in entities">
                <div v-on:click.prevent="onSelect(entity.id)" :class="['card eclm-card', isSelected(entity.id)]"
                     id="entity.id"
                     style="max-width:250px;">
                    <img :src="entity.thumbnail.url" class="card-img-top" :alt="entity.name">
                    <div class="card-body">
                        <h5 class="card-title" style="padding-bottom: 0px;">{{ entity.name }}</h5>
                        <div class="eclm-badges-container">
                            <template v-if="entity.milliliter">
                            <span class="badge badge-pill badge-info eclm-badge">
                                {{entity.milliliter}}ml
                            </span>
                            </template>
                            <template v-if="entity.product">
                                <span class="badge badge-pill badge-primary">
                                    {{entity.product.currency}}{{ entity.product.price.gross }}
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else style="padding: 5px; max-width: 50%">
            <p>Zur zeit sind keine Label Me Produkte im bestand Vorhanden, aber wir arbeiten dran.</p>
        </div>
    </div>
</template>

<script>
    import {bus} from "../../main";

    export default {

        props: ['entities', 'getEntityEvent', 'selectedEntity', 'phrase', 'header'],


        methods: {
            onSelect(id) {
                bus.$emit(this.getEntityEvent, id);
            },

            isSelected(id) {
                let classes = ''
                if (this.selectedEntity === id) {
                    classes += ' card-selected';
                }
                return classes;
            }
        }
    }
</script>
