<template>
    <div class="card-deck">
        <div v-for="e in entity">
            <Card :id="e.id" :url="e.thumbnail.url" :title="e.name"></Card>
        </div>
    </div>
</template>

<script>
    import Card from './Card.vue'

    export default {
        components: {
            Card
        },
        props: ['entity']
    }
</script>
