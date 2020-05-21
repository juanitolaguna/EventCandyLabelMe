import './page/eclm-plugin-list';
// import './page/eclm-plugin-detail';
// import './page/eclm-plugin-create';


Shopware.Module.register('eclm-plugin', {
    type: 'plugin',
    name: 'eclm-plugin',
    color: '#982aff',
    icon: 'default-object-puzzle-piece',
    title: 'Event Candy Label Me',
    description: 'Manage plugin here.',

    routes: {
        list: {
            component: 'eclm-plugin-list',
            path: 'list'
        },
        detail: {
            component: 'eclm-plugin-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'eclm.plugin.list'
            }
        },
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
        path: 'eclm.plugin.list',
        icon: 'default-object-puzzle-piece',
        position: 100
    }],

});
