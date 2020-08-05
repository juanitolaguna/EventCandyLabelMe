## Vagrant worflow

build storefront
- `./psh.phar storefront:build`
copy storefront files from:
- `EventCandyLabelMe/src/Resources/app/storefront/dist`


build administration
- ` ./psh.phar administration:build`
copy administration files from:
- `EventCandyLabelMe/src/Resources/public/administration`

remove all DS_Store files if exists then zip. Plugin ready for upload.
- `find . -name '.DS_Store' -type f -delete`


"vue": "^2.6.11",
"vue-loader": "^15.9.2",
"vue-template-compiler": "^2.6.11",
