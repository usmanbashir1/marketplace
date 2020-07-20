# m2-weltpixel-frontend-options

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-frontend-options git git@github.com:rusdragos/m2-weltpixel-frontend-options.git
$ composer require weltpixel/m2-weltpixel-frontend-options:dev-master
```
Note: Composer installation only available for WeltPixel internal use for the moment as the repositos are not public. However, there is a work around that will allow you to install the product via composer, described in the article below: https://support.weltpixel.com/hc/en-us/articles/115000216654-How-to-use-composer-and-install-Pearl-Theme-or-other-WeltPixel-extensions


Manually:

Copy the zip into app/code/WeltPixel/FrontendOptions directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_FrontendOptions --clear-static-content
$ php bin/magento setup:upgrade
```

### Important

Please delete the view/frontend/web/css/source/_module.less file, it is not used anymore
All the custom less generation was moved to view/frontend/web/css/source/_store_STORECODE_extend.less file
