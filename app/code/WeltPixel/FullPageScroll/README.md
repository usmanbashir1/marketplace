# m2-weltpixel-fullpagescroll

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-full-page-scroll git git@github.com:rusdragos/m2-full-page-scroll.git
$ composer require weltpixel/module-fullpagescroll:dev-master
```
Note: Composer installation only available for WeltPixel internal use for the moment as the repositos are not public. However, there is a work around that will allow you to install the product via composer, described in the article below: https://support.weltpixel.com/hc/en-us/articles/115000216654-How-to-use-composer-and-install-Pearl-Theme-or-other-WeltPixel-extensions


Manually:

Copy the zip into app/code/WeltPixel/FullPageScroll directory

#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_FullPageScroll --clear-static-content
$ php bin/magento setup:upgrade
```
<hr/>
Insert in all desired with FullPageScroll CMS Pages following content
 
 ```
 {{block class="WeltPixel\FullPageScroll\Block\FullPageScroll" name="fullpagescroll" template="WeltPixel_FullPageScroll::fullpagescroll.phtml"}}
 ```
 <br/>
 Create each section as a CMS Block.
 <br/>
 CMS Block Identifier must have the following format:
 <br/>
 fullpagescroll_cmspageurlkey_sectionorder
 
 ```
 fullpagescroll_home_section1
 fullpagescroll_home_section2
 ```
 <br/>
 Each section created as a CMS Block can containe all type of content (text, images, ...).
 <br/>
 The first image from the CMS Block is always set as a background of current section and the rest of the images will be displayed as section content.
 
 <br/>
 <br/>
 The sections of a specific page will be displayed in alphabetical order based on CMS Block <strong>Identifier</strong> name
