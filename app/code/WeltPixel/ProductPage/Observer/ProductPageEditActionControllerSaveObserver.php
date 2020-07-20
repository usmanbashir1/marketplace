<?php

namespace WeltPixel\ProductPage\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * ProductPageEditActionControllerSaveObserver observer
 */
class ProductPageEditActionControllerSaveObserver implements ObserverInterface
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
     * var \WeltPixel\ProductPage\Helper\Data
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
    protected $_mobileBreakPoint;

    /**
     * @var string
     */
    protected $_mobileBreakPointUnder;


    /**
     * @var \WeltPixel\ProductPage\Model\ProductPageFactory
     */
    protected $productPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

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
     * ProductPageEditActionControllerSaveObserver constructor.
     *
     * @param \WeltPixel\ProductPage\Helper\Data $helper
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\ProductPage\Model\ProductPageFactory $productPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Proxy $session
     */
    public function __construct(
        \WeltPixel\ProductPage\Helper\Data $helper,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\ProductPage\Model\ProductPageFactory $productPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Proxy $session
    )
    {
        $this->_helper = $helper;
        $this->_frontendHelper = $frontendHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->productPageFactory = $productPageFactory;
        $this->jsonHelper = $jsonHelper;
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
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_storeCollection = $this->_storeManager->getStores();
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_ProductPage');
        foreach ($this->_storeCollection as $store) {

            $this->_mobileBreakPoint = $this->_frontendHelper->getBreakpointM($store->getData('store_id'));
            $this->_mobileBreakPointUnder = ((int) $this->_frontendHelper->getBreakpointM($store->getData('store_id')) - 1) . 'px';

            $imageAreaWidth = trim($this->_helper->getImageAreaWidth($store->getData('store_id')));
            $productAreaWidth = trim($this->_helper->getProductInfoAreaWidth($store->getData('store_id')));
            $removeSwatchTooltip = $this->_helper->removeSwatchTooltip($store->getData('store_id'));
            $swatchOptions = $this->_helper->getSwatchOptions($store->getData('store_id'));
            $cssOptions = $this->_helper->getCssOptions($store->getData('store_id'));
            $backgroundArrows = $this->_helper->getBackgroundArrows($store->getData('store_id'));
            $galleryThumbsNavDir = $this->_helper->getGalleryNavDir($store->getData('store_id'));
            $generatedCssDirectoryPath = DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'weltpixel_product_store_' .
                $store->getData('code') . '.less';

            $this->collectionData($store->getData('store_id'));

            $content = $this->_generateContent($imageAreaWidth, $productAreaWidth);
            $content .= $this->_generateSwatchCss($swatchOptions);
            $content .= $this->_generateBackgroundArrows($backgroundArrows);
            if ($galleryThumbsNavDir == 'vertical') {
                $content .= $this->_generateVerticalNavDirFix();
            }


            if ($removeSwatchTooltip) {
                $content .= $this->_addRemoveSwatchTooltipCss();
            }

            $content .= $this->_generateCssOptions($cssOptions, $store->getData('store_id'));

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


        /* Store view specific less generation */
        $this->_generateStoreViewSpecificLess();

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
     * Generate the less css content for the product page options
     *
     * @param sring $imageAreaWidth
     * @param string $productAreaWidth
     * @return string
     */
    private function _generateContent($imageAreaWidth, $productAreaWidth)
    {
        $content = '/* Generated Less from WeltPixel_ProductPage */' . PHP_EOL;
        $content .= '.theme-pearl.catalog-product-view { .block.related, .block.upsell {clear: both} }' . PHP_EOL;

        if (strlen($imageAreaWidth)) {
            $content .= $this->_getAreaWidthCss($imageAreaWidth);
        }

        if (strlen($productAreaWidth)) {
            $content .= $this->_getProductAreaWidthCss($productAreaWidth);
        }

        return $content;
    }

    /**
     * @param $imageAreaWidth
     * @return string
     */
    private function _getAreaWidthCss($imageAreaWidth)
    {
        $content = "
        @media (min-width: $this->_mobileBreakPoint) {
            .theme-pearl.catalog-product-view.page-layout-1column .product.media {
              width: $imageAreaWidth;
            }
            .theme-pearl.catalog-product-view.page-layout-2columns-left .product.media,
            .theme-pearl.catalog-product-view.page-layout-2columns-right .product.media,
            .theme-pearl.catalog-product-view.page-layout-3columns .product.media {
              width: $imageAreaWidth;
            }
        }
        ";

        return $content;
    }

    /**
     * @param $productAreaWidth
     * @return string
     */
    private function _getProductAreaWidthCss($productAreaWidth)
    {
        $content = "
        @media (min-width: $this->_mobileBreakPoint) {
            .theme-pearl.catalog-product-view.page-layout-1column .product-info-main {
                width: $productAreaWidth;
            }
            .theme-pearl.catalog-product-view.page-layout-2columns-left .product-info-main,
            .theme-pearl.catalog-product-view.page-layout-2columns-right .product-info-main,
            .theme-pearl.catalog-product-view.page-layout-3columns .product-info-main {
              width: $productAreaWidth;
            }
        }
        ";

        return $content;
    }


    /**
     * @return string;
     */
    private function _addRemoveSwatchTooltipCss()
    {
        $content = '.theme-pearl.catalog-product-view .swatch-option-tooltip {display: none !important;}' . PHP_EOL;
        return $content;
    }


    /**
     * @param array $swatchOptions
     * @return string
     */
    private function _generateSwatchCss($swatchOptions)
    {
        $radius = $swatchOptions['radius'];
        $width = $swatchOptions['width'];
        $height = $swatchOptions['height'];
        $lineHeight = $swatchOptions['line_height'];
        $fontSize = $swatchOptions['font_size'];

        $hWidth = (int)$width + 6 . 'px';
        $hHeight = (int)$height + 6 . 'px';
        $icon_content = '\e116';

        $content = "
        .theme-pearl.catalog-product-view {
            .swatch-option {
                outline: none !important;
                border: none !important;
                position: relative;
                border-radius: $radius;
                -moz-border-radius: $radius;
                -webkit-border-radius: $radius;
                width: $width;
                height: $height;
                min-width: $width;
                &.image{
                    &:before {
		                visibility: hidden;
		                position: absolute;
		                top: -3px;
		                left: -3px;
		                z-index: 0;
		                width: $hWidth;
		                height: $hHeight;
		                border: 1px solid transparent;
		                border-radius: $radius !important;
		                -moz-border-radius: $radius !important;
		                -webkit-border-radius: $radius !important;
		                content: '';
		                -webkit-box-sizing: border-box;
					    -moz-box-sizing: border-box;
					    box-sizing: border-box;
		            }
		            &:after {
		                visibility: hidden;
		                position: absolute;
		                top: -3px;
		                left: -3px;
		                z-index: 1;
		                width: $hWidth;
		                height: $hHeight;
		                line-height: $hHeight;
		                font-family: lined-icons;
		                speak: none;
		                font-style: normal;
		                font-weight: 900;
		                font-variant: normal;
		                font-size: 0.6vw;
		                text-transform: none;
		                text-align: center;
		                -webkit-font-smoothing: antialiased;
		                -moz-osx-font-smoothing: grayscale;
		                content: '$icon_content';
		                color: #ffffff;
		            }
                   &.selected{
                    position: relative;
                    overflow: visible;
                    &:before {
                        visibility: visible;
                        border: 1px solid #999999;
                    }

                   }
                   &:hover {
		                position: relative;
		                overflow: visible;
		                &:before {
		                    visibility: visible;
		                    border: 1px solid #999999;
		                }
		            }
                    &.disabled{
                        &:after {
                            visibility: visible;
                            content: '';
                        }
                    }
                }
	            &.color {
	                &:before {
		                visibility: hidden;
		                position: absolute;
		                top: -3px;
		                left: -3px;
		                z-index: 0;
		                width: $hWidth;
		                height: $hHeight;
		                border: 1px solid transparent;
		                border-radius: $radius !important;
		                -moz-border-radius: $radius !important;
		                -webkit-border-radius: $radius !important;
		                content: '';
		                -webkit-box-sizing: border-box;
					    -moz-box-sizing: border-box;
					    box-sizing: border-box;
		            }
		            &:after {
		                visibility: hidden;
		                position: absolute;
		                top: -3px;
		                left: -3px;
		                z-index: 1;
		                width: $hWidth;
		                height: $hHeight;
		                line-height: $hHeight;
		                font-family: lined-icons;
		                speak: none;
		                font-style: normal;
		                font-weight: 900;
		                font-variant: normal;
		                font-size: 0.6vw;
		                text-transform: none;
		                text-align: center;
		                -webkit-font-smoothing: antialiased;
		                -moz-osx-font-smoothing: grayscale;
		                content: '$icon_content';
		                color: #ffffff;
		            }
		            &:hover {
		                position: relative;
		                overflow: visible;
		                &:before {
		                    visibility: visible;
		                    border: 1px solid #999999;
		                }
		            }
		            &.selected {
		                position: relative;
		                overflow: visible;
		                &:before {
		                    visibility: visible;
		                    border: 1px solid #999999;
		                }
		                &:after {
		                    visibility: visible;
		                    font-size: $fontSize;
		                }
		                &[option-tooltip-value='#fff'],
		                &[option-tooltip-value='#ffffff'] {
		                    &:after {
		                        color: #000000;
		                    }
		                }
		            }
		             &.disabled{
		              &:after {
		               visibility: visible;
		               content: '';
		              }
		            }
		            &[option-tooltip-value='#fff'],
		            &[option-tooltip-value='#ffffff'] {
		                border: 1px solid #cccccc !important;
		                &:before {
		                    top: -4px;
		                    left: -4px;
		                }
		                &:after {
		                    top: -4px;
		                    left: -4px;
		                }
		            }
	            }
	            &.text {
	                line-height: $lineHeight;
	                padding: 0;
	                font-size: $fontSize;
	                margin-right: 15px;
		            &:before {
		                visibility: hidden;
		                position: absolute;
		                top: -3px;
		                left: -3px;
		                z-index: 0;
		                width: $hWidth;
		                height: $hHeight;
		                border: 1px solid transparent !important;
		                border-radius: $radius !important;
		                -moz-border-radius: $radius !important;
		                -webkit-border-radius: $radius !important;
		                content: '';
		                -webkit-box-sizing: border-box;
					    -moz-box-sizing: border-box;
					    box-sizing: border-box;
		            }
		            &:hover {
		                position: relative;
		                overflow: visible;
		                &:before {
		                    visibility: visible;
		                    border: 1px solid #999999 !important;
		                }
		            }
		            &.selected {
		                position: relative;
		                overflow: visible;
		                &:before {
		                    visibility: visible;
		                    border: 1px solid #999999 !important;
		                }
		            }
	            }
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
     * @param array $cssOptions
     * @param int $storeId
     * @return string
     */
    private function _generateCssOptions($cssOptions, $storeId)
    {
        $productVersion = $this->_helper->productVersion($storeId);
        $thumbBorder = $cssOptions['thumbnail_border'];
        $tabActiveBg = $cssOptions['tab_active_background'];
        $tabBg = $cssOptions['tab_background'];
        $tabTextActiveColor = $cssOptions['tab_text_active_color'];
        $tabTextColor = $cssOptions['tab_text_color'];
        $tabContainerPadding = '';
        if($productVersion == '1' ||  $productVersion == '3'){
            $tabContainerPadding = 'padding:'.$cssOptions['tab_container_padding'];
        }

        $cssBG = $cssOptions['page_background_color'];
        $page_background_color = $cssBG != '' ? true : false;
        $pbc = '';
        $rgba = '';
        if ($page_background_color) {
            $pbc = 'background-color: ' . $cssBG . ' !important;';
            $rgba = 'background-color: rgba(' . implode(",", array_values($this->hex2rgb($cssBG))) . ',0.8) !important;';
        }

        $cssBGT = $cssOptions['page_background_color_top_v3'];
        $page_background_color_top_v3 = $cssBGT != '' ? true : false;
        if ($page_background_color_top_v3) {
            $page_background_color_top_v3 = 'background-color: ' . $cssBGT . ';';
        } else {
            $page_background_color_top_v3 = '';
        }

        $page_background_color_bottom_v3 = $cssOptions['page_background_color_bottom_v3'] != '' ? true : false;
        if ($page_background_color_bottom_v3) {
            $pbct = 'background-color: ' . $cssOptions['page_background_color_bottom_v3'] . ';';
            $rgbat = 'background-color: rgba(' . implode(",", array_values($this->hex2rgb($cssOptions['page_background_color_bottom_v3']))) . ',0.8) !important;';
        } else {
            $pbct = '';
            $rgbat = '';
        }

        $content = "

        .product-page-v2 {
            &.theme-pearl.catalog-product-view #pre-div,
	        &.theme-pearl.catalog-product-view {
                $pbc
            }
	        &.theme-pearl.catalog-product-view #maincontent .product-items.owl-carousel.owl-center .owl-nav {
	            .owl-prev,
	            .owl-next {
		            $rgba
				}
				&.fullscreen {
					.owl-prev,
	                .owl-next {
						background-color: transparent !important;
						&:hover {
							background-color: transparent !important;
						}
					}
				}
	        }
        }

		.theme-pearl.product-page-v3,
		.theme-pearl.product-page-v4 {
			.page-wrapper {
				overflow-x: hidden;
			}
			.fotorama_arr, .fotoramathumb_arr { background-color: transparent; }
			.column.main {
				position: relative;
				&:before {
					content: '';
					position: absolute;
					top: 0;
					left: -50%;
					width: 200%;
					height: 100%;
					$pbct
					z-index: -1;
				}
				.product-info-main {
					padding-top: 7%;
					margin-top: 0px;
					&.cart-summary {
						padding-top: 75px !important;
					}
				}
				.product.info.detailed {
					padding-top: 25px;
				}
			}
			.product.media.product_v4,
			.product_v3 {
				position: relative;
				display: inline-block;
				width: 100%;
				.product.media,
				.product-info-main {
					position: relative;
				}
				&:before {
					content: '';
					position: absolute;
					top: 0;
					left: -50%;
					width: 200%;
					height: 100%;
					$page_background_color_top_v3
				}
			}
			&.catalog-product-view #pre-div {
                $pbct
            }
            &.catalog-product-view #maincontent .product-items.owl-carousel.owl-center .owl-nav {
	            .owl-prev,
	            .owl-next {
	                $rgbat
				}
				&.fullscreen {
					.owl-prev,
	                .owl-next {
						background-color: transparent !important;
						&:hover {
							background-color: transparent !important;
						}
					}
				}
	        }
	        &.catalog-product-view .swatch-option.text {
	            background-color: white;
	        }
		}

        .theme-pearl.catalog-product-view {
            .fotorama__thumb-border {
                border: 1px solid $thumbBorder;
            }
            .fotorama__nav-wrap--vertical{
                  .fotorama__nav--thumbs .fotorama__nav__frame {
                  .fotorama__thumb {
                        border-bottom: 2px solid $cssBG;
                  }
                  &:last-of-type {
                    .fotorama__thumb {
                        border: none;
                    }
                  }
			    }
            }


            .product.data.items > .item.title > .switch,
            .product.data.items > .item.title > .switch:visited{
                background-color: $tabBg !important;
                color: $tabTextColor !important;
            }

            .product.data.items > .item.title:not(.disabled) > .switch:active,
            .product.info.detailed > .items > .item,
            .product.data.items > .item.title.active > .switch,
            .product.data.items > .item.title.active > .switch:focus,
            .product.data.items > .item.title.active > .switch:hover {
                background-color: $tabActiveBg !important;
                color: $tabTextActiveColor !important;
            }

            .product.info.detailed > .items > .item.title.active {
                position: relative;
                &:before {
                    content: '';
                    position: absolute;
                    background-color: $tabActiveBg !important;
                    width: ~\"(100% - 2px)\";
                    height: 1px;
                    bottom: -1px;
                    left: 1px;
                }
            }

            .product.info.detailed > .items > .item.content {
                 input,
                 select,
                 textarea {
                    background-color: $tabActiveBg !important;
                }
            }
            .page-main{
                .columns{
                    .product.info.detailed{
                        .product.data.items > .item.content {
                            $tabContainerPadding;
                         @media (max-width: $this->_mobileBreakPointUnder) {
                            padding: 10px;
                            margin-top:0px !important;
                         }
                        }
                    }
                }
            }


            .product.info.detailed.toggle-bg{
                .data.item.content.togglec{
                    $tabContainerPadding;
                }
            }

        }
        .theme-pearl.product-page-v4 {
	        @media (min-width: $this->_mobileBreakPoint) {
	            .product-info-main {
	                min-width: 450px;
	                right: 20px !important;
	            }
	        }
        }

        .actions .paypal-button{
           position: sticky;
        }

        ";

        return $content;
    }

    private function _generateBackgroundArrows($backgroundArrows)
    {
        if ($backgroundArrows != '') {
            $rgba = 'background-color: rgba(' . implode(",", array_values($this->hex2rgb($backgroundArrows))) . ',0.3) !important;';
            $rgbah = 'background-color: rgba(' . implode(",", array_values($this->hex2rgb($backgroundArrows))) . ',0.5) !important;';
        } else {
            $rgba = 'background-color: transparent !important;';
            $rgbah = 'background-color: transparent !important;';
        }
        $content = "
    	    .fotorama__arr {
                $rgba
    	        &:hover {
    	            $rgbah
    	        }
    	    }
    	    .fotorama__nav__frame--dot:focus .fotorama__dot {
			    box-shadow: none;
			    &:after {
			        box-shadow: none;
			    }
			}
    	";

        return $content;
    }

    /**
     * @return string
     */
    private function _generateVerticalNavDirFix() {
        $content = "
         	  .theme-pearl.catalog-product-view {
                    .product.media {
                        .fotorama__wrap {
                            .fotorama__arr--next {
                                right: 0 !important;
                            }
                        }
         	        }
         	  }
    	";

        return $content;
    }

    /**
     * @return void
     */
    protected function _generateStoreViewSpecificLess()
    {
        $content = '/* Generated Less from WeltPixel_ProductPage */' . PHP_EOL;

        $lessTemplateAccordion = $this->_dirReader->getModuleDir('', 'WeltPixel_ProductPage') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'accordiontabs_template.less';
        $lessTemplateList = $this->_dirReader->getModuleDir('', 'WeltPixel_ProductPage') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'listtabs_template.less';
        $lessTemplate = $this->_dirReader->getModuleDir('', 'WeltPixel_ProductPage') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'template.less';

        $content .= file_get_contents($lessTemplate);

        $lessVariables = $this->_getLessVariables();

        foreach ($this->_storeCollection as $store) {
            $lessValues = $this->_getLessValues($store);
            $tabsLayout = $this->_helper->getTabsLayout($store->getId());


            if ($tabsLayout == \WeltPixel\ProductPage\Model\Config\Source\TabsLayout::TAB_ACCORDION) {
                $content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplateAccordion));
            }
            if ($tabsLayout == \WeltPixel\ProductPage\Model\Config\Source\TabsLayout::TAB_LIST) {
                $content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplateList));
            }

        }

        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_ProductPage');

        $lessPath =
            DIRECTORY_SEPARATOR . 'frontend' .
            DIRECTORY_SEPARATOR . 'web' .
            DIRECTORY_SEPARATOR . 'css' .
            DIRECTORY_SEPARATOR . 'source' .
            DIRECTORY_SEPARATOR . '_module.less';

        $writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
        $file = $writer->openFile($lessPath, 'w');
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

    /**
     * @return array
     */
    private function _getLessVariables()
    {
        return array(
            '@storeViewClass'
        );
    }

    /**
     * @param \Magento\Store\Model\Store
     * @return array
     */
    private function _getLessValues(\Magento\Store\Model\Store $store)
    {
        $storeCode = $store->getData('code');
        $storeClassName = '.theme-pearl.store-view-' . preg_replace('#[^a-z0-9-_]+#', '-', strtolower($storeCode));

        return array(
            $storeClassName
        );
    }

    public function collectionData($store)
    {

        $scopeConfig = 'weltpixel_product_page';
        $adminOptions = $this->_scopeConfig->getValue($scopeConfig, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        $version = '';
        foreach ($adminOptions as $key => $values) {
            $name = $scopeConfig . '_' . $key . '_';
            if ($key != 'version') {
                foreach ($values as $key => $value) {
                    $configs[] = [
                        'id' => $name . $key,
                        'value' => $value
                    ];
                }
            } else {
                $version = $values['version'];
            }
        }

        $options = $this->jsonHelper->jsonEncode($configs);;

        $productPage = $this->productPageFactory->create();
        $productPage->loadByVersionAndStore($version, $store);

        $productPage->setData('version_id', $version);
        $productPage->setData('store_id', $store);
        $productPage->setData('values', $options);

        try {
            $productPage->save();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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
        $rgb = array($r, $g, $b);
        return $rgb;
    }
}
