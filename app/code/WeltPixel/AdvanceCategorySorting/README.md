# m2-weltpixel-category-sorting

### Installation

Dependencies:
 - m2-weltpixel-backend
 - magento-catalog

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-category-sorting git git@github.com:rusdragos/m2-weltpixel-category-sorting.git
$ composer require weltpixel/m2-weltpixel-category-sorting:dev-master
```

Manually:

Copy the zip into app/code/WeltPixel/AdvanceCategorySorting directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_AdvanceCategorySorting --clear-static-content
$ php bin/magento setup:upgrade
```
