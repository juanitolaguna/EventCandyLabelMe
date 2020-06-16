import './page/eclm-index';


//Event
import './view/eclm-event/eclm-event-list';
import './view/eclm-event/eclm-event-detail';
import './view/eclm-event/eclm-event-create';

//Candy
import './view/eclm-candy/eclm-candy-list';
import './view/eclm-candy/eclm-candy-detail';
import './view/eclm-candy/eclm-candy-create';

//Label
import './view/eclm-label/eclm-label-detail';
import './view/eclm-label/eclm-label-create';

//Package
import './view/eclm-package/eclm-package-list';
import './view/eclm-package/eclm-package-detail';
import './view/eclm-package/eclm-package-create';

//CandyPackage
import './view/eclm-candy-package/eclm-candy-package-list';
import './view/eclm-candy-package/eclm-candy-package-detail';



//Components
import './components/eclm-media-field';

import deDE from '../snippet/de-DE.json';
import enGB from '../snippet/en-GB.json';


Shopware.Module.register('eclm-plugin', {
    type: 'plugin',
    name: 'eclm-plugin',
    color: '#982aff',
    icon: 'default-object-puzzle-piece',
    title: 'eclm.general.mainMenuItemGeneral',
    description: 'eclm.general.descriptionTextModule',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'eclm-index',
            path: 'index',
            redirect: {
                name: 'eclm.plugin.index.event'
            },
            children: {
                candy: {
                    component: 'eclm-candy-list',
                    path: 'candy',
                },
                event: {
                    component: 'eclm-event-list',
                    path: 'event',
                },
                package: {
                    component: 'eclm-package-list',
                    path: 'package'
                },
                candyPackages: {
                    component: 'eclm-candy-package-list',
                    path: 'candy-packages'
                }
            }
        },

        // Candy
        candyDetail: {
            component: 'eclm-candy-detail',
            path: 'candy-detail/:id',
            meta: {
                parentPath: 'eclm.plugin.index.candy'
            }
        },
        candyCreate: {
            component: 'eclm-candy-create',
            path: 'candy-create',
            meta: {
                parentPath: 'eclm.plugin.index.candy'
            }
        },

        // Event
        eventDetail: {
            component: 'eclm-event-detail',
            path: 'event-detail/:id',
            meta: {
                parentPath: 'eclm.plugin.index.event'
            }
        },
        eventCreate: {
            component: 'eclm-event-create',
            path: 'event-create',
            meta: {
                parentPath: 'eclm.plugin.index.event'
            }
        },

        // Label
        labelDetail: {
            component: 'eclm-label-detail',
            path: 'label-detail/:id',
        },
        labelCreate: {
            component: 'eclm-label-create',
            path: 'label-create/:eventId'
        },

        // Package
        packageDetail: {
            component: 'eclm-package-detail',
            path: 'package-detail/:id',
            meta: {
                parentPath: 'eclm.plugin.index.package'
            }
        },
        packageCreate: {
            component: 'eclm-package-create',
            path: 'package-create',
            meta: {
                parentPath: 'eclm.plugin.index.package'
            }
        },

        //Candy&Package
        candyPackageDetail: {
            component:'eclm-candy-package-detail',
            path: 'candy-package-detail/:id',
            meta: {
                parentPath: 'eclm.plugin.index.candyPackages'
            }
        }



    },

    // nav entry
    navigation: [{
        label: 'eclm.general.mainMenuItemGeneral',
        color: '#982AFF',
        path: 'eclm.plugin.index',
        icon: 'default-object-puzzle-piece',
        position: 100
    }]
});
