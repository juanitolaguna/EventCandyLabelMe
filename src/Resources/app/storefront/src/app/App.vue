<template>
    <div>
        <p class="example">{{ msg }}</p>
        <template v-if="data">
            <p>{{ data.address }}</p>
            <p>{{ data.name }}</p>
        </template>

    </div>

</template>

<script>
    import StoreApiClient from 'src/service/store-api-client.service';

    export default {
        data() {
            return {
                msg: 'Hello world!',
                data: null,
            }
        },

        computed: {
            httpClient() {
                return new StoreApiClient(window.accessKey);
            }
        },
        created() {
            this.componentCreated();
        },

        methods: {
            componentCreated() {
                this.httpClient.get('store-api/v{version}/eclm/get-events', (response) => {
                    this.data = JSON.parse(response);
                });
            }
        }
    }
</script>
