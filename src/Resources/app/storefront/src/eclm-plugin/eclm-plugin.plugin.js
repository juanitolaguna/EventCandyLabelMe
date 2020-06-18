import Plugin from 'src/plugin-system/plugin.class';
import StoreApiClient from 'src/service/store-api-client.service';


export class EclmPlugin extends Plugin {
    init() {
        // console.log(window.accessKey);
        //
        // const httpClient = new StoreApiClient(window.accessKey);
        // httpClient.get('store-api/v{version}/eclm/get-events', (response) => {
        //     console.log(response);
        // });
    }
}
