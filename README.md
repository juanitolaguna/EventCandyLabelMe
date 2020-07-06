## Vagrant worflow

build storefront
- ` ./psh.phar storefront:build`
copy storefront files from:
- `EventCandyLabelMe/src/Resources/app/storefront/dist`


build administration
- ` ./psh.phar administration:build`
copy administration files from:
- `EventCandyLabelMe/src/Resources/public/administration`

remove all DS_Store files if exists then zip. Plugin ready for upload.
