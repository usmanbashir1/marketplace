<?php

namespace WeltPixel\CategoryPage\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * CategoryPageEditActionControllerSaveObserver observer
 */
class CategoryPageEditActionControllerSaveObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_dirReader;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    protected $_writeFactory;

    /**
     * var \WeltPixel\CategoryPage\Helper\Data
     */
    protected $_helper;

    /**
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Store collection
     * storeId => \Magento\Store\Model\Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * @var string
     */
    protected $_listingBreakPoint;

    /**
     * @var string
     */
    protected $_xxsBreakPoint;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session\Proxy
     */
    protected $_session;

    /**
     * Constructor
     *
     * @param \WeltPixel\CategoryPage\Helper\Data $helper
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Proxy $session
     */
    public function __construct(
        \WeltPixel\CategoryPage\Helper\Data $helper,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Proxy $session
    ) {
        $this->_helper = $helper;
        $this->_frontendHelper = $frontendHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_session = $session;
    }

    /**
     * Save for each sore the css options in file
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_storeCollection = $this->_storeManager->getStores();
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_CategoryPage');

        foreach ($this->_storeCollection as $store) {
            $this->_listingBreakPoint = $this->_frontendHelper->getBreakpointM($store->getData('store_id'));
            $this->_xxsBreakPoint = $this->_frontendHelper->getBreakpointXXS($store->getData('store_id'));

            $displaySwatchTooltip = $this->_helper->displaySwatchTooltip($store->getData('store_id'));
            $ItemOptions = $this->_helper->getProductListingItemOptions($store->getData('store_id'));
            $NameOptions = $this->_helper->getProductListingNameOptions($store->getData('store_id'));
            $reviewOptions = $this->_helper->getProductListingReviewOptions($store->getData('store_id'));
            $priceOptions = $this->_helper->getProductListingPriceOptions($store->getData('store_id'));
            $productsPerLine = $this->_helper->getProductsPerLine($store->getData('store_id'));
            $swatchLayeredOptions = $this->_helper->getLayeredNavigationSwatchOptions($store->getData('store_id'));
            $swatchListingOptions = $this->_helper->getProductListingSwatchOptions($store->getData('store_id'));
            $displaySwatches = $this->_helper->displaySwatches($store->getData('store_id'));
            $toolbarOptions = $this->_helper->getToolbarOptions($store->getData('store_id'));
            $descriptionOptions = $this->_helper->getCategoryDescriptionOptions($store->getData('store_id'));
            $defaultLineHeight = (int) $this->_helper->getDefaultLineHeight($store->getData('store_id')) ?
                (int) $this->_helper->getDefaultLineHeight($store->getData('store_id')) : 20;

            $generatedCssDirectoryPath = DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'weltpixel_category_store_' .
                $store->getData('code') . '.less';

            $content = $this->_generateContent($displaySwatchTooltip, $productsPerLine);
            $content .= $this->_generateDescriptionOptions($descriptionOptions, $defaultLineHeight);
            $content .= $this->_generateItemOptions($ItemOptions);
            $content .= $this->_generateNameOptions($NameOptions);
            $content .= $this->_generateReviewOptions($reviewOptions);
            $content .= $this->_generatePriceOptions($priceOptions);
            $content .= $this->_generateSwatchCss($swatchListingOptions, $displaySwatches, '.products-grid', 0);
            $content .= $this->_generateSwatchCss($swatchListingOptions, $displaySwatches, '.products-list', 0);
            $content .= $this->_generateSwatchCss($swatchLayeredOptions, $displaySwatches, '.sidebar #layered-filter-block .filter-options', 1);
            $content .= $this->_generateSwatchCss($swatchLayeredOptions, $displaySwatches, '.page-layout-1column #layered-filter-block .filter-options', 0);
            $content .= $this->_generateToolbarCss($toolbarOptions);

            /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
            $writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
            /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
            $file = $writer->openFile($generatedCssDirectoryPath, 'w');
            try {
                $file->lock();
                try {
                    $file->write($content);
                } finally {
                    $file->unlock();
                }
            } finally {
                $file->close();
            }
        }

        /** Set only the notifications if triggered from admin save */
        $event = $observer->getEvent();
        if ($event instanceof \Magento\Framework\Event) {
            $eventName = $observer->getEvent()->getData();
            if ($eventName) {
                $url = $this->_urlBuilder->getUrl('adminhtml/cache');
                $message = __('Please regenerate Pearl Theme LESS/CSS files from <a href="%1">Cache Management Section</a>', $url);
                $this->_messageManager->addWarning($message);
                $this->_session->setWeltPixelCssRegeneration(true);
            }
        }

        return $this;
    }

    /**
     * Convert HEX in RGB
     *
     * @param string $hex
     * @return string
     */
    private function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    /**
     * Generate the less css content for the category page options
     *
     * @param boolean $displaySwatchTooltip
     * @param integer $productsPerLine
     * @return string
     */
    private function _generateContent($displaySwatchTooltip, $productsPerLine)
    {
        $content = '/* Generated Less from WeltPixel_CategoryPage */' . PHP_EOL;
        $content .= '
.canvas_icons {
	@size: 25px;
	position: relative;
	top: 0;
	margin: 0 5px 0 0;
	padding: 0;
	width: @size;
	height: @size;
	line-height: @size !important;
	text-align: center;
	font-size: 20px;

	display: inline-block;
	font-family: font-icons;
	speak: none;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	text-transform: none;
	line-height: inherit;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale;

	overflow: hidden;
	&:before {
		font-family: inherit;
		font-size: inherit;
		color: inherit;

	}
}
        ';

        if (!$displaySwatchTooltip) {
            $content .= '.swatch-option-tooltip {display: none !important}' . PHP_EOL;
        }

        $content .= $this->_getProductsPerLineCss($productsPerLine);

        return $content;
    }

    /**
     * @param integer $productsPerLine
     * @return string
     */
    private function _getProductsPerLineCss($productsPerLine)
    {
        $width = (100 / $productsPerLine) - 1;
        $productsPerLinePV = $productsPerLine + 1;
        $widthPV = (100 / $productsPerLinePV) - 1;

        /** For 2 images per row show for all devices 2 per row **/
        $breakPoint = $this->_listingBreakPoint;

        if ($productsPerLine == 2) {
            $breakPoint = $this->_xxsBreakPoint;
        }

        $content = "
@media (min-width: $breakPoint), print {
    .page-products .grid.products-grid :not(.widget-product-grid) .product-item { width: $width% !important; }
    .catalog-product-view .grid.products-grid .product-item { width: $widthPV% !important; }
    .catalog-product-view .grid.products-grid .owl-item .product-item { width: 100% !important; }
    .page-products .grid.products-grid .product-items,
    .catalog-product-view .grid.products-grid .product-items { margin: 0 !important; }
    .page-products .grid.products-grid .product-item {
        width: $width%;
        margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        padding: 0;
        .product_image {
            .product-item-actions{
                .actions-secondary{
                    display: block
                }
            }
        }";
        switch ($productsPerLine) {
            case 2:
                $content .= "
        &:nth-child(2n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }
        &:nth-child(3n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }";
                break;
            case 3:
                $content .= "
        &:nth-child(3n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }
        &:nth-child(4n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }";
                break;
            case 4:
                $content .= "
                 &:nth-child(3n+1) {
            margin-left: 0;
        }
        &:nth-child(4n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }";
                break;
            case 5:
                $content .= "
                 &:nth-child(3n+1) {
            margin-left: 0;
        }
        &:nth-child(4n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }
        &:nth-child(5n+1) {
            margin-left: calc(~\"(100% - $productsPerLine * $width%) / $productsPerLine\");
        }";
                break;
        }
        $content .= "
    }
    .catalog-product-view .grid.products-grid .product-item {
        width: $widthPV%;
        margin-left: calc(~\"(100% - $productsPerLinePV * $widthPV%) / $productsPerLinePV\");
        padding: 0;";
        switch ($productsPerLinePV) {
            case 4:
                $content .= "
        &:nth-child(3n+1) {
            margin-left: calc(~\"(100% - $productsPerLinePV * $widthPV%) / $productsPerLinePV\");
        }";
                break;
            case 5:
                $content .= "
                &:nth-child(3n+1) {
            margin-left: 0;
        }
        &:nth-child(4n+1) {
            margin-left: calc(~\"(100% - $productsPerLinePV * $widthPV%) / $productsPerLinePV\");
        }";
                break;
            case 6:
                $content .= "
                 &:nth-child(3n+1) {
            margin-left: 0;
        }
        &:nth-child(4n+1) {
            margin-left: calc(~\"(100% - $productsPerLinePV * $widthPV%) / $productsPerLinePV\");
        }
        &:nth-child(5n+1) {
            margin-left: calc(~\"(100% - $productsPerLinePV * $widthPV%) / $productsPerLinePV\");
        }";
                break;
        }

        $content .= "
    }

    .page-products .grid.products-grid .product-item { width: $width% }
    .catalog-product-view .grid.products-grid .product-item { width: $widthPV% }
    .catalog-product-view .grid.products-grid .owl-item .product-item { width: 100% !important; }
    .page-products .grid.products-grid .product-item { width: $width% }
    .catalog-product-view .grid.products-grid .product-item { width: $widthPV% }
    .catalog-product-view .grid.products-grid .owl-item .product-item { width: 89% !important; margin: 0; }
}
        ";
        return $content;
    }

    /**
     * @param array $swatchOptions
     * @param array $displaySwatches
     * @param array $wrapperClass
     * @param array $sidebar
     * @return string
     */
    private function _generateSwatchCss($swatchOptions, $displaySwatches, $wrapperClass, $sidebar)
    {
        $radius = $swatchOptions['radius'];
        $width = $swatchOptions['width'];
        $height = $swatchOptions['height'];
        $fontSize = $swatchOptions['font_size'];
        $align = $swatchOptions['swatch_align'];

        if ($wrapperClass == '.products-list') {
            $align = 'left';
        }

        $showOnHover = '';
        if ($sidebar === 0 && $displaySwatches == 2) {
            $showOnHover = "
    .product-item {
        .product-item-info {
            .swatch-attribute {
                visibility: hidden !important;
                text-align:  $align !important; // $align
                .swatch-attribute-options {
                    display: inline-block;
                    text-align:  $align !important; // $align
                    padding: 2px 0;
                    .swatch-option {
                        float: left !important;
                        clear: none !important;
                    }
                }
            }
        }
        .swatch-attribute-options {
            .swatch-option {
                &.text,
                &.color {
                    &:before {
                        visibility: hidden !important;
                    }
                    &:after {
                        visibility: hidden !important;
                    }
                }
            }
        }
        &:hover {
            .swatch-attribute-options {
                visibility: visible !important;
                .swatch-option {
                    &.text,
                    &.color {
                        &:before {
                            visibility: visible !important;
                        }
                        &.selected {
                            &:after {
                                visibility: visible !important;
                            }
                        }
                        &.disabled {
                                &:after {
                                visibility: visible !important;
                                content: '';
                            }
                        }
                    }
                }
            }
        }
    }
            ";
        } elseif ($sidebar === 0 && $displaySwatches == 0) {
            $showOnHover = '
    .product-item {
        .product-item-info {
            .swatch-attribute-options {
                display: none !important;
            }
        }
    }
            ';
        } elseif ($sidebar === 0 && $displaySwatches == 1) {
            $showOnHover = "
    .product-item {
        .product-item-info {
            .swatch-attribute-options {
                text-align:  $align !important; // $align
                .swatch-option {
                    float: left !important;
                    clear: none !important;
                    display: inline-block !important;
                }
            }
        }
    }
            ";
        }
        $hWidth = (int)$width - 4 . 'px';
        $hHeight = (int)$height - 4 . 'px';
        $lineHeightText = (int)$height - 2 . 'px';
        $icon_content = '\e116';
        $content = "
$wrapperClass {
    .swatch-attribute {
        text-align: $align !important; // $align
        .swatch-attribute-options {
            text-align: $align !important; // $align
            display: inline-block !important;
            padding: 2px 0;
            > a {
                float: none !important;
                display: inline-block !important;
                &:hover {
                    background: none;
                }
            }
        }
    }
    .product-item {
        .swatch-attribute-options {
            .swatch-option {
                outline: none !important;
                position: relative;
                &.color,
                &.image{
                    border: none !important;
                    &:before {
                        visibility: hidden;
                        position: absolute;
                        top: 2px;
                        left: 2px;
                        z-index: 0;
                        width: $hWidth;
                        height: $hHeight;
                        border: 3px solid transparent;
                        border-radius: $radius !important;
                        -moz-border-radius: $radius !important;
                        -webkit-border-radius: $radius !important;
                        visibility: visible;
                        content: '';
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                    }
                    &:after {
                        visibility: hidden;
                        position: absolute;
                        top: 0;
                        left: 0;
                        z-index: 1;
                        width: $width;
                        height: $height;
                        line-height: $height;
                        font-family: lined-icons;
                        speak: none;
                        font-style: normal;
                        font-weight: 900;
                        font-variant: normal;
                        font-size: 12px;
                        text-transform: none;
                        text-align: center;
                        -webkit-font-smoothing: antialiased;
                        -moz-osx-font-smoothing: grayscale;
                        content: \"$icon_content\";
                        color: #ffffff;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                    }
                    &:hover {
                        position: relative;
                        overflow: visible;
                        &:before {
                            visibility: visible;
                            border: 3px solid white;
                        }
                    }
                     &.disabled {
                        &:after {
                            visibility: visible !important;
                            content: '';
                        }
                    }
                    &.selected {
                        position: relative;
                        overflow: visible;
                        &:before {
                            visibility: visible;
                            border: 3px solid white;
                        }
                        &:after {
                            visibility: visible;
                        }
                        &[option-tooltip-value='#fff'],
                        &[option-tooltip-value='#ffffff'] {
                            &:after {
                                color: #000000;
                            }
                        }
                    }
                    &[option-tooltip-value='#fff'],
                    &[option-tooltip-value='#ffffff'] {
                        border: 1px solid #cccccc !important;
                        &:before {
                            top: 0px;
                            left: 0px;
                        }
                        &:after {
                            top: 0px;
                            left: 0px;
                        }
                    }
                }
                &.text {
                    position: relative;
                    &.selected,
                    &:hover {
                        position: relative;
                        overflow: visible;
                        border: 2px solid #999999 !important;
                    }
                }
            }
        }
    }
    $showOnHover
    .swatch-option {
        border-radius: $radius !important;
        -moz-border-radius: $radius !important;
        -webkit-border-radius: $radius !important;
        width: $width !important;
        height: $height !important;
        min-width: $width !important;
        margin: 3px !important;
    }
    .swatch-option.text {
        line-height: $lineHeightText !important;
        padding: 0 !important;
        font-size: $fontSize !important;
        margin: 3px !important;
    }

    .swatch-option:not(.image):not(.color):not(.text) {
                border: 1px solid #ddd !important;
                &:hover{
                    border: 1px solid #999 !important;
                }
                &.selected{
                    border: 1px solid #999 !important;
                }
            }
}
        ";

        return $content;
    }

    /**
     * @param array $swatchItemOptions
     * @return string
     */
    private function _generateItemOptions($swatchItemOptions)
    {
        $borderWidth = !empty($swatchItemOptions['border_width']) ? $swatchItemOptions['border_width'] : '25px';
        $borderColor = !empty($swatchItemOptions['border_color']) ? $swatchItemOptions['border_color'] : '#cccccc';
        $boxShadow = !empty($swatchItemOptions['box_shadow']) ? $swatchItemOptions['box_shadow'] : '0px 5px 30px 0px rgba(0,0,0,0.05)';
        $content = "
.products-grid {
    .product-item {
        .product-item-info {
            width: 100% !important;
            position: relative;
            border: $borderWidth solid transparent !important;
            &:hover {
                -webkit-box-shadow: $boxShadow !important;
                -moz-box-shadow: $boxShadow !important;
                box-shadow: $boxShadow !important;
                border: $borderWidth solid $borderColor !important;
                position: relative;
            }
        }
        &:hover {
            background: none repeat scroll 0 0 rgba(255, 255, 255, 1);
        }
    }
    &.wishlist {
	    .product-item {
	        .product-item-info {
                border: 1px solid transparent !important;
	            &:hover {
	                position: relative;
		            box-shadow: 3px 3px 4px 0 rgba(0, 0, 0, 0.3) !important;
                    border: 1px solid rgba(0, 0, 0, 0.3) !important;
			        .product-item-inner {
			            box-shadow: 3px 3px 4px 0 rgba(0, 0, 0, 0.3);
                        border: 1px solid rgba(0, 0, 0, 0.3);
			            border-top: none;
			        }
	            }
	        }
	    }
    }
	&.products-upsell {
		.product-item {
	        .product-item-info {
                    border-bottom: $borderWidth solid transparent !important;
	            &:hover {
                    border-bottom: $borderWidth solid $borderColor !important;
	            }
	        }
	    }
	}
}
.products-list {
	.product-item {
		&:hover {
			-webkit-box-shadow: $boxShadow !important;
            -moz-box-shadow: $boxShadow !important;
            box-shadow: $boxShadow !important;
            border: $borderWidth solid $borderColor !important;
		}
	}
}
        ";

        return $content;
    }

    private function _generateDescriptionOptions($descriptionOptions, $defaultLineHeight)
    {
        $content = '';
        if ($descriptionOptions['enable_show_more']) {
            $descriptionHeight = (int) $descriptionOptions['show_more_lines'] ? (int) $descriptionOptions['show_more_lines'] * $defaultLineHeight . 'px' : 'unset';
            $content .= ".category-view .category-description {max-height: $descriptionHeight; margin: 0 auto 10px;}";
        }

        return $content;
    }

    /**
     * @param array $NameOptions
     * @return string
     */
    private function _generateNameOptions($NameOptions)
    {
        $nameAlign = !empty($NameOptions['name_align']) ? $NameOptions['name_align'] : 'left';
        $fontSize = !empty($NameOptions['font_size']) ? $NameOptions['font_size'] : '16px';
        $color = !empty($NameOptions['color']) ? $NameOptions['color'] : '#000000';

        $content = "
.products-grid {
    .product-item {
        .product-item-name {
            text-align: $nameAlign !important;
            a {
                text-align: $nameAlign !important;
                font-size: $fontSize !important;
                color: $color !important;
            }
        }
    }
}
.products-list {
    .product-item {
        .product-item-name {
            text-align: left !important; // $nameAlign
            a {
                text-align: left !important; // $nameAlign
                font-size: $fontSize !important;
                color: $color !important;
            }
        }
    }
}
        ";

        return $content;
    }

    /**
     * @param array $priceOptions
     * @return string
     */
    private function _generatePriceOptions($priceOptions)
    {
        $priceAlign = !empty($priceOptions['price_align']) ? $priceOptions['price_align'] : 'left';
        $priceFontSize = !empty($priceOptions['price_font_size']) ? $priceOptions['price_font_size'] : '16px';
        $priceColor = !empty($priceOptions['price_color']) ? $priceOptions['price_color'] : '#000000';
        $specialPriceFontSize = !empty($priceOptions['special_price_font_size']) ? $priceOptions['special_price_font_size'] : '16px';
        $specialPriceColor = !empty($priceOptions['special_price_color']) ? $priceOptions['special_price_color'] : '#000000';

        $content = "
.products-grid {
    .product-item {
        .product-item-details {
            .price-box {
                text-align: $priceAlign !important;
                .price-container {
                    .price-label {
                        display: block !important;
                    }
                    .price {
                        font-size: $priceFontSize !important;
                        color: $priceColor !important;
                    }
                }
                .old-price {
                    margin-left: 10px;
                    .price-container {
                        .price-label {
                            display: none;
                        }
                        .price {
                            font-size: $priceFontSize !important;
                            color: $priceColor !important;
                        }
                    }
                }
                .special-price {
                    .price-container {
                        .price-label {
                            display: none;
                        }
                        .price {
                            font-size: $specialPriceFontSize !important;
                            color: $specialPriceColor !important;
                        }
                    }
                }
            }
        }
    }
}
.products-list {
    .product-item {
        .product-item-details {
            .price-box {
                text-align: left !important;
                .price-container {
                    .price-label {
                        display: none;
                    }
                    .price {
                        font-size: $priceFontSize !important;
                        color: $priceColor !important;
                    }
                }
                .old-price {
                    margin-left: 10px;
                    .price-container {
                        .price-label {
                            display: none;
                        }
                        .price {
                            font-size: $priceFontSize !important;
                            color: $priceColor !important;
                        }
                    }
                }
                .special-price {
                    .price-container {
                        .price-label {
                            display: none;
                        }
                        .price {
                            font-size: $specialPriceFontSize !important;
                            color: $specialPriceColor !important;
                        }
                    }
                }
            }
        }
    }
}
        ";

        return $content;
    }

    /**
     * @param array $reviewOptions
     * @return string
     */
    private function _generateReviewOptions($reviewOptions)
    {
        $reviewAlign = $reviewOptions['review_align'];

        $content = "
.products-grid {
    .product-item {
        .product-item-details {
            .product-reviews-summary {
                text-align: $reviewAlign !important;
                .reviews-actions,
                .rating-summary {
                     text-align: left !important;
                }
            }
        }
    }
}
.products-list {
    .product-item {
        .product-item-details {
            .product-reviews-summary {
                text-align: left !important; // $reviewAlign
                .reviews-actions,
                .rating-summary {
                     text-align: left !important;
                }
            }
        }
    }
}
        ";

        return $content;
    }

    /**
     * @param $toolbarOptions
     * @return string
     */
    private function _generateToolbarCss($toolbarOptions)
    {
        $background_color = !empty($toolbarOptions["background_color"]) ? 'background-color:' . $toolbarOptions["background_color"] . ' !important;' : '';
        $border_color = !empty($toolbarOptions["border_color"]) ? 'border: 1px solid ' . $toolbarOptions["border_color"] . ';' : '';
        $padding = !empty($toolbarOptions["padding"]) ? 'padding: ' . $toolbarOptions["padding"] . ';' : '';
        $label_font_size = !empty($toolbarOptions["label_font_size"]) ? 'font-size: ' . $toolbarOptions["label_font_size"] . ';' : '';
        $label_font_color = !empty($toolbarOptions["label_font_color"]) ? 'color: ' . $toolbarOptions["label_font_color"] . ';' : '';
        $grid_list_background_color = !empty($toolbarOptions["grid_list_background_color"]) ? 'background-color: ' . $toolbarOptions["grid_list_background_color"] . ';' : '';
        $grid_list_background_hover_color = !empty($toolbarOptions["grid_list_background_hover_color"]) ? 'background-color: ' . $toolbarOptions["grid_list_background_hover_color"] . ';' : '';
        $grid_list_background_active_color = !empty($toolbarOptions["grid_list_background_active_color"]) ? 'background-color: ' . $toolbarOptions["grid_list_background_active_color"] . ';' : '';
        $grid_list_border_color = !empty($toolbarOptions["grid_list_border_color"]) ? 'border: 1px solid ' . $toolbarOptions["grid_list_border_color"] . ';' : '';
        $grid_list_border_hover_color = !empty($toolbarOptions["grid_list_border_hover_color"]) ? 'border: 1px solid ' . $toolbarOptions["grid_list_border_hover_color"] . ';' : '';
        $grid_list_border_active_color = !empty($toolbarOptions["grid_list_border_active_color"]) ? 'border: 1px solid ' . $toolbarOptions["grid_list_border_active_color"] . ';' : '';
        $grid_list_icon_size = !empty($toolbarOptions["grid_list_icon_size"]) ? 'font-size: ' . $toolbarOptions["grid_list_icon_size"] . ' !important;' : '';
        $grid_list_icon_color = !empty($toolbarOptions["grid_list_icon_color"]) ? 'color: ' . $toolbarOptions["grid_list_icon_color"] . ';' : '';
        $grid_list_icon_hover_color = !empty($toolbarOptions["grid_list_icon_hover_color"]) ? 'color: ' . $toolbarOptions["grid_list_icon_hover_color"] . ' !important;' : '';
        $grid_list_icon_active_color = !empty($toolbarOptions["grid_list_icon_active_color"]) ? 'color: ' . $toolbarOptions["grid_list_icon_active_color"] . ';' : '';
        $select_background_color = !empty($toolbarOptions["select_background_color"]) ? $toolbarOptions["select_background_color"] : 'transparent';
        $select_background_focus_color = !empty($toolbarOptions["select_background_focus_color"]) ? $toolbarOptions["select_background_focus_color"] : 'transparent';
        $select_border_width = !empty($toolbarOptions["select_border_width"]) ? $toolbarOptions["select_border_width"] : 0;
        $select_border_color = !empty($toolbarOptions["select_border_color"]) ? 'border-color: ' . $toolbarOptions["select_border_color"] . ';' : 'border-color: #999999;';
        $select_border_focus_color = !empty($toolbarOptions["select_border_focus_color"]) ? 'border-color: ' . $toolbarOptions["select_border_focus_color"] . ';' : '';
        $select_border_radius = !empty($toolbarOptions["select_border_radius"]) ? 'border-radius: ' . $toolbarOptions["select_border_radius"] . ';' : '';
        $select_font_size = !empty($toolbarOptions["select_font_size"]) ? 'font-size: ' . $toolbarOptions["select_font_size"] . ';' : '';
        $select_min_height_val = !empty($toolbarOptions["select_font_size"]) ? (int)$toolbarOptions["select_font_size"] + 12 : '';
        $select_line_height_val = !empty($toolbarOptions["select_font_size"]) ? (int)$toolbarOptions["select_font_size"] + 6 : '';
        $select_min_height = !empty($select_min_height_val) ? 'min-height: ' . $select_min_height_val . 'px;' : '';
        $select_line_height = !empty($select_line_height_val) ? 'line-height: ' . $select_line_height_val . 'px;' : '';
        $select_font_color = !empty($toolbarOptions["select_font_color"]) ? 'color: ' . $toolbarOptions["select_font_color"] . ';' : '';
        $select_arrow_color = !empty($toolbarOptions["select_arrow_color"]) ? $toolbarOptions["select_arrow_color"] : '';
        $select_arrow_hover_color = !empty($toolbarOptions["select_arrow_hover_color"]) ? $toolbarOptions["select_arrow_hover_color"] : '';
        $select_option_font_color = !empty($toolbarOptions["select_option_font_color"]) ? 'color: ' . $toolbarOptions["select_option_font_color"] . ';' : '';
        $direction_font_size = !empty($toolbarOptions["direction_font_size"]) ? 'font-size: ' . $toolbarOptions["direction_font_size"] . ';' : '';
        $direction_font_color = !empty($toolbarOptions["direction_font_color"]) ? 'color: ' . $toolbarOptions["direction_font_color"] . ';' : '';
        $direction_font_hover_color = !empty($toolbarOptions["direction_font_hover_color"]) ? 'color: ' . $toolbarOptions["direction_font_hover_color"] . ';' : '';

        $pagination_font_size = !empty($toolbarOptions["pagination_font_size"]) ? 'font-size: ' . $toolbarOptions["pagination_font_size"] . ';' : '';
        $pagination_font_size_abs_val = !empty($toolbarOptions["pagination_font_size"]) ? (int)$toolbarOptions["pagination_font_size"] : '';
        $pagination_font_line_height_val = !empty($pagination_font_size_abs_val) ? $pagination_font_size_abs_val - 6 : '';
        $pagination_font_line_height = !empty($pagination_font_size_abs_val) ? 'line-height: ' . $pagination_font_line_height_val . 'px;' : '';
        $pagination_min_width = !empty($pagination_font_size_abs_val) ? 'min-width: ' . $pagination_font_size_abs_val . 'px;' : '';
        $pagination_min_height = !empty($pagination_font_size_abs_val) ? 'min-height: ' . $pagination_font_size_abs_val . 'px;' : '';

        $pagination_font_color = !empty($toolbarOptions["pagination_font_color"]) ? 'color: ' . $toolbarOptions["pagination_font_color"] . ';' : '';
        $pagination_font_hover_color = !empty($toolbarOptions["pagination_font_hover_color"]) ? 'color: ' . $toolbarOptions["pagination_font_hover_color"] . ';' : '';
        $pagination_font_active_color = !empty($toolbarOptions["pagination_font_active_color"]) ? 'color: ' . $toolbarOptions["pagination_font_active_color"] . ';' : '';
        $pagination_background_color = !empty($toolbarOptions["pagination_background_color"]) ? 'background-color: ' . $toolbarOptions["pagination_background_color"] . ';' : 'background-color: transparent;';
        $pagination_background_hover_color = !empty($toolbarOptions["pagination_background_hover_color"]) ? 'background-color: ' . $toolbarOptions["pagination_background_hover_color"] . ';' : 'background-color: transparent;';
        $pagination_background_active_color = !empty($toolbarOptions["pagination_background_active_color"]) ? 'background-color: ' . $toolbarOptions["pagination_background_active_color"] . ';' : 'background-color: transparent;';
        $pagination_border_color = !empty($toolbarOptions["pagination_border_color"]) ? 'border: 1px solid ' . $toolbarOptions["pagination_border_color"] . ';' : 'border: 1px solid transparent;';
        $pagination_border_hover_color = !empty($toolbarOptions["pagination_border_hover_color"]) ? 'border: 1px solid ' . $toolbarOptions["pagination_border_hover_color"] . ';' : 'border: 1px solid transparent;';
        $pagination_border_active_color = !empty($toolbarOptions["pagination_border_active_color"]) ? 'border: 1px solid ' . $toolbarOptions["pagination_border_active_color"] . ';' : 'border: 1px solid transparent;';
        $pagination_border_radius = !empty($toolbarOptions["pagination_border_radius"]) ? 'border-radius: ' . $toolbarOptions["pagination_border_radius"] . ';' : '';
        $pagination_next_prev_font_size = !empty($toolbarOptions["pagination_next_prev_font_size"]) ? 'font-size: ' . $toolbarOptions["pagination_next_prev_font_size"] . ';' : '';
        $pagination_next_prev_font_color = !empty($toolbarOptions["pagination_next_prev_font_color"]) ? 'color: ' . $toolbarOptions["pagination_next_prev_font_color"] . ';' : '';
        $pagination_next_prev_font_hover_color = !empty($toolbarOptions["pagination_next_prev_font_hover_color"]) ? 'color: ' . $toolbarOptions["pagination_next_prev_font_hover_color"] . ';' : '';
        $pagination_next_prev_background_color = !empty($toolbarOptions["pagination_next_prev_background_color"]) ? 'background-color: ' . $toolbarOptions["pagination_next_prev_background_color"] . ';' : 'background-color: transparent;';
        $pagination_next_prev_background_hover_color = !empty($toolbarOptions["pagination_next_prev_background_hover_color"]) ? 'background-color: ' . $toolbarOptions["pagination_next_prev_background_hover_color"] . ';' : 'background-color: transparent;';

        $pagination_next_prev_border_color = !empty($toolbarOptions["pagination_next_prev_border_color"]) ? 'border: 1px solid ' . $toolbarOptions["pagination_next_prev_border_color"] . ';' : 'border: 1px solid transparent;';
        $pagination_next_prev_border_hover_color = !empty($toolbarOptions["pagination_next_prev_border_hover_color"]) ? 'border: 1px solid ' . $toolbarOptions["pagination_next_prev_border_hover_color"] . ';' : 'border: 1px solid transparent;';

        $select_arrow_c = '';
        if ($select_arrow_color == 'transparent') {
            $select_arrow_c = $select_arrow_color;
        } else {
            $select_arrow_c = 'rgb(' . implode(",", array_values($this->hex2rgb($select_arrow_color))) . ')';
        }

        $select_arrow_hover_c = '';
        if ($select_arrow_hover_color == 'transparent') {
            $select_arrow_hover_c = $select_arrow_hover_color;
        } else {
            $select_arrow_hover_c = 'rgb(' . implode(",", array_values($this->hex2rgb($select_arrow_hover_color))) . ')';
        }

        $backgroundSelectImage = 'background: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' height=\'10\' width=\'16\'><line x1=\'0\' y1=\'0\' x2=\'8\' y2=\'8\' style=\'stroke:' . $select_arrow_c . ';stroke-width:2\' /><line x1=\'16\' y1=\'0\' x2=\'8\' y2=\'8\' style=\'stroke:' . $select_arrow_c . ';stroke-width:2\' /></svg>") no-repeat 90% 50% ' . $select_background_color . ';';
        $backgroundSelectImageHover = 'background: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' height=\'10\' width=\'16\'><line x1=\'0\' y1=\'0\' x2=\'8\' y2=\'8\' style=\'stroke:' . $select_arrow_hover_c . ';stroke-width:2\' /><line x1=\'16\' y1=\'0\' x2=\'8\' y2=\'8\' style=\'stroke:' . $select_arrow_hover_c . ';stroke-width:2\' /></svg>") no-repeat 90% 50% ' . $select_background_focus_color . ';';

        if (!$select_border_width) {
            $select_border_width = [];
        } else {
            try {
                $select_border_width_json = json_decode($select_border_width);
                /** magento 2.2 removed serialization  */
                if ($select_border_width_json && ($select_border_width != $select_border_width_json)) {
                    $select_border_width = json_decode($select_border_width, true);
                    $select_border_width = $select_border_width['<%- _id %>'];
                } else {
                    $select_border_width = unserialize($select_border_width)['<%- _id %>'];
                }
            } catch (\Exception $ex) {
                $select_border_width = [];
            }
        }

        $selectOBW = [];
        $true = false;
        foreach ($select_border_width as $sbw) {
            if ($sbw) {
                $true = true;
            }
            $selectOBW[] .= $sbw;
        }
        $selectOBW = implode(' ', $selectOBW);
        $select_border_width = strlen(trim($true)) ? 'border-width: ' . $selectOBW . ';' : 'border-width: 0px 0px 1px 0px;';

        $content = "
    #layered-filter-block {
        &.filter {
            .block-content.filter-content {
                .block-subtitle {
                    border-style: solid;
                    border-width: 0 0 1px 0;
                    $select_border_color
                }
            }
        }
        .block-content.filter-content {
            .filter-options {
                .filter-options-item {
                    .filter-options-title {
                        border-style: solid;
                        border-width: 0 0 1px 0;
                        $select_border_color
                        &:after {
                            color: $select_arrow_c;
                        }
                        &:hover {
                            &:after {
                                color: $select_arrow_hover_c;
                            }
                        }
                    }
                }
            }
        }
    }
    .toolbar.toolbar-products {
        $background_color
        $border_color
        $padding
        .label_style() {
            $label_font_size
            $label_font_color
        }
        label {
            .label_style;
            span {
                .label_style;
            }
        }
        select.sorter-options,
        select.limiter-options {
            $select_font_size
            $select_font_color
            $select_border_width
            $select_min_height
            $select_line_height
            border-style: solid;
            $select_border_color
            box-shadow: unset;
            $backgroundSelectImage
            $select_border_radius
            background-size: 12px;
            padding: 0px 30px 2px 5px;
            &:focus {
                $select_border_focus_color
                $backgroundSelectImageHover
                background-size: 12px;
            }
            option {
                $select_option_font_color
                &:hover,
                &:checked {
                    background-color: #eeeeee;
                }
            }
        }
        select.limiter-options {
            padding: 0 15px 2px 5px;
        }
        .modes {
            #modes-label {
                .label_style;
            }
            .modes-mode {
                $grid_list_background_color
                $grid_list_border_color
                $grid_list_icon_color
                &:before {
                    $grid_list_icon_color
                    $grid_list_icon_size
                }
                &:hover {
                    $grid_list_background_hover_color
                    $grid_list_border_hover_color
                    &:before {
                        $grid_list_icon_hover_color
                        $grid_list_icon_size
                    }
                }
                &.active {
                    $grid_list_background_active_color
                    $grid_list_border_active_color
                    $grid_list_icon_active_color
                    &:before {
                        $grid_list_icon_active_color
                        $grid_list_icon_size
                    }
                }
            }
        }
        p#toolbar-amount {
            .label_style;
        }
        .pages {
            #paging-label {
                .label_style;
            }
            ul.pages-items {
                li {
                    &.item {
                        a.page {
                            $pagination_font_size
                            $pagination_font_color
                            $pagination_background_color
                            $pagination_border_color
                            $pagination_border_radius
                            $pagination_min_width
                            $pagination_min_height
                            &:hover {
                                $pagination_font_hover_color
                                $pagination_background_hover_color
                                $pagination_border_hover_color
                            }
                            span {
                                width: 100%;
                                $pagination_font_line_height
                            }
                        }
                        &.current {
                            strong.page {
                                $pagination_font_size
                                $pagination_font_active_color
                                $pagination_background_active_color
                                $pagination_border_active_color
                                $pagination_border_radius
                                $pagination_min_width
                                $pagination_min_height
                                span {
                                    width: 100%;
                                    $pagination_font_line_height
                                }
                            }
                        }
                        &.pages-item-previous,
                        &.pages-item-next {
                            a {
                                $pagination_next_prev_font_size
                                $pagination_next_prev_font_color
                                $pagination_next_prev_background_color
                                $pagination_next_prev_border_color
                                $pagination_border_radius
                                &:before {
                                    $pagination_next_prev_font_color
                                    $pagination_next_prev_font_size

                                }
                                &:hover {
                                    $pagination_next_prev_background_hover_color
                                    $pagination_next_prev_border_hover_color
                                    &:before {
                                        $pagination_next_prev_font_hover_color
                                    }
                                }
                            }

                        }
                    }
                }
            }
        }
        .field.limiter {
            label {
                .label_style;
                span {
                    .label_style;
                }
            }
            .limiter-text {
                .label_style;
            }
        }
        .toolbar-sorter.sorter {
            label {
                .label_style;
            }
            .sorter-action {
                &:before {
                    $direction_font_size
                    $direction_font_color
                }
                &:hover {
                    &:before {
                        $direction_font_hover_color
                    }
                }
            }
        }
    }
        ";

        return $content;
    }
}
