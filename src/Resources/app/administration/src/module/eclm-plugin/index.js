import './page/eclm-index';
import './view/eclm-candy/eclm-candy-list';
import './view/eclm-event/eclm-event-list';


Shopware.Module.register('eclm-plugin', {
    type: 'plugin',
    name: 'eclm-plugin',
    color: '#982aff',
    icon: 'default-object-puzzle-piece',
    title: 'Event Candy Label Me',
    description: 'Manage plugin here.',

    routes: {
        index: {
            component: 'eclm-index',
            path: 'index',
            // redirect: {
            //     name: 'eclm.plugin.index.list'
            // },
            children: {
                candy: {
                    component: 'eclm-candy-list',
                    path: 'candy',
                    meta: {
                        parentPath: 'eclm.plugin.index'
                    }
                },
                event: {
                    component: 'eclm-event-list',
                    path: 'event',
                }
            }

        }


        // list: {
        //     component: 'eclm-plugin-list',
        //     path: 'list'
        // },
        // detail: {
        //     component: 'eclm-plugin-detail',
        //     path: 'detail/:id',
        //     meta: {
        //         parentPath: 'eclm.plugin.list'
        //     }
        // },
        // create: {
        //     component: 'eclm-plugin-create',
        //     path: 'create',
        //     meta: {
        //         parentPath: 'eclm.plugin.list'
        //     }
        // }
    },
    navigation: [{
        label: 'Event Candy Label Me',
        color: '#982AFF',
        path: 'eclm.plugin.index',
        icon: 'default-object-puzzle-piece',
        position: 100
    }],

    // routeMiddleware(next, currentRoute) {
    //     if (currentRoute.name === 'eclm.plugin.index') {
    //         console.log(currentRoute);
    //
    //         currentRoute.children.push({
    //             name: 'eclm.plugin.index.list',
    //             path: '/eclm/plugin/index/list',
    //             component: 'eclm-plugin-list',
    //             meta: {
    //                 parentPath: "eclm.plugin.index"
    //             }
    //         });
    //     }
    //     next(currentRoute);
    // }
});
