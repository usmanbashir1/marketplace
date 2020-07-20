<?php

namespace WeltPixel\CustomHeader\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * CustomHeaderEditActionControllerSaveObserver observer
 */
class CustomHeaderEditActionControllerSaveObserver implements ObserverInterface
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
     * var \WeltPixel\CustomHeader\Helper\Data
     */
    protected $_helper;

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
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_frontendHelper;

    /**
     * @var string
     */
    protected $_mobileBreakPoint;

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
    protected  $_session;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * CustomHeaderEditActionControllerSaveObserver constructor.
     * @param \WeltPixel\CustomHeader\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Proxy $session
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     */
    public function __construct(
        \WeltPixel\CustomHeader\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Proxy $session,
        \Magento\Framework\Filesystem\DirectoryList $dir
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_frontendHelper = $frontendHelper;
        $this->_messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_session = $session;
        $this->_dir = $dir;
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
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_CustomHeader');

        foreach ($this->_storeCollection as $store) {

            $this->_mobileBreakPoint = $this->_frontendHelper->getBreakpointM($store->getData('store_id'));

            $generatedCssDirectoryPath = DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'weltpixel_custom_header_' .
                $store->getData('code') . '.less';

            $content = $this->_generateContent($store->getData('store_id'));

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
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }


    /**
     * Generate the less css content for the header options
     *
     * @param int $storeId
     * @return string
     */
    private function _generateContent($storeId)
    {
        $content = '/* Generated Less from WeltPixel_CustomHeader */' . PHP_EOL;

        $content .= $this->_generateLogoLess($storeId);


        $globalPromoTextColor = $this->_helper->getGlobalPromoTextColor($storeId);
        $globalPromoBackgroundColor = $this->_helper->getGlobalPromoBackgroundColor($storeId);

        $topHeaderWidth = $this->_helper->getTopHeaderWidth($storeId);

        $topHeaderLinkColor = $this->_helper->getTopHeaderLinkColor($storeId);
        $topHeaderActiveLinkColor = $this->_helper->getTopHeaderActiveLinkColor($storeId);
        $topHeaderHoverLinkColor = $this->_helper->getTopHeaderHoverLinkColor($storeId);

        $topHeaderSubmenuLinkColor = $this->_helper->getTopHeaderSubmenuLinkColor($storeId);
        $topHeaderSubmenuHoverLinkColor = $this->_helper->getTopHeaderSubmenuHoverLinkColor($storeId);

        $topHeaderTextColor = $this->_helper->getTopHeaderTextColor($storeId);
        $topHeaderBackgroundColor = $this->_helper->getTopHeaderBackgroundColor($storeId);
        $topHeaderBorderBottomColor = $this->_helper->getTopHeaderBorderBottomColor($storeId);
        $middleHeaderWidth = $this->_helper->getMiddleHeaderWidth($storeId);
        $middleHeaderBackgroundColor = $this->_helper->getMiddleHeaderBackgroundColor($storeId);
        $bottomHeaderWidth = $this->_helper->getBottomHeaderWidth($storeId);
        $bottomHeaderPadding = $bottomHeaderWidth;
        $bottomHeaderBackgroundColor = $this->_helper->getBottomHeaderBackgroundColor($storeId);
        $bottomHeaderMenuFontSizeL0 = $this->_helper->getBottomHeaderMenuFontSizeL0($storeId);
        $bottomHeaderMenuFontSizeL1 = $this->_helper->getBottomHeaderMenuFontSizeL1($storeId);
        $bottomHeaderMenuFontSizeL2 = $this->_helper->getBottomHeaderMenuFontSizeL2($storeId);
        $bottomHeaderLinkColor = $this->_helper->getBottomHeaderLinkColor($storeId);
        $bottomHeaderHoverLinkColor = $this->_helper->getBottomHeaderHoverLinkColor($storeId);
        $bottomNavigationShadow = $this->_helper->getBottomNavigationShadow($storeId);

        // sticky header
        $stickyIsEnabled = $this->_helper->stickyHeaderIsEnabled($storeId);
        $stickyHeaderBackgroundColor = null;
        $stickyHeaderElementsColor = null;
        $stickyHeaderElementsHoverColor = null;
        $stickyNavigationBorderColor = null;
        $stickyNavigationBorderHoverColor = null;
        $stickySearchBorderColor = null;
        $stickySearchBackgroundColor = $this->_helper->getSerachOptionsBackground($storeId);
        if ($stickyIsEnabled) {
            $stickyAdvancedColors = $this->_helper->advancedColorsIsEnabled($storeId);
            if ($stickyAdvancedColors) {
                $stickyHeaderBackgroundColor = $this->_helper->getStickyHeaderBackgroundColor($storeId);
                $stickyHeaderElementsColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickyHeaderElementsHoverColor = $this->_helper->getStickyHeaderElementsHoverColor($storeId);
                $stickyNavigationBorderColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickyNavigationBorderHoverColor = $this->_helper->getStickyHeaderElementsHoverColor($storeId);
                $stickySearchBorderColor = $this->_helper->getStickyHeaderElementsColor($storeId);
                $stickySearchBackgroundColor = $this->_helper->getStickyHeaderBackgroundColor($storeId);
            }
        }

        $serachOptionsWidth = $this->_helper->getSerachOptionsWidth($storeId);
        $serachOptionsHeight = $this->_helper->getSerachOptionsHeight($storeId);
        $serachOptionsBorderWidth = $this->_helper->getSerachOptionsBorderWidth($storeId);
        $serachOptionsBorderStyle = $this->_helper->getSerachOptionsBorderStyle($storeId);
        $serachOptionsBorderColor = $this->_helper->getSerachOptionsBorderColor($storeId);
        $serachOptionsBackground = $this->_helper->getSerachOptionsBackground($storeId);
        $serachOptionsColor = $this->_helper->getSerachOptionsColor($storeId);
        $serachOptionsPlaceHolderColor = $this->_helper->getSerachOptionsPlaceHolderColor($storeId);
        $serachOptionsFontSize = $this->_helper->getSerachOptionsFontSize($storeId);
        $backgroundColorSearchv2 = $this->_helper->getBackgroundColorSearchv2($storeId);
        $backgroundOpacitySearchv2 = $this->_helper->getBackgroundOpacitySearchv2($storeId);
        $inputBackgroundColor = $this->_helper->getInputBackgroundColor($storeId);
        $mainSearchElementsColor = $this->_helper->getMainSearchElementsColor($storeId);
        $magnifierBackgroundColor = $this->_helper->getMagnifierBackgroundColor($storeId);
        $inputBorders = $this->_helper->getInputBorders($storeId);
        $searchInputFontSize = $this->_helper->getSearchInputFontSize($storeId);
        $borderWidth = $this->_helper->getBorderWidth($storeId);


        $headerIconSize = $this->_helper->getHeaderIconSize($storeId);
        $headerIconColor = $this->_helper->getHeaderIconColor($storeId);
        $headerIconHoverColor = $this->_helper->getHeaderIconHoverColor($storeId);
        // ---

        $globalPromoTextColor = strlen(trim($globalPromoTextColor)) ? 'color: ' . $globalPromoTextColor . ';' : '';
        $globalPromoBackgroundColor = strlen(trim($globalPromoBackgroundColor)) ? 'background-color: ' . $globalPromoBackgroundColor . ';' : '';

        $topHeaderWidth = strlen(trim($topHeaderWidth)) ? 'max-width:' . $topHeaderWidth . ' !important;' : '';

        $topHeaderLinkColorImportant = strlen(trim($topHeaderLinkColor)) ? 'color:' . $topHeaderLinkColor . '!important;' : '';

        $topHeaderLinkColor = strlen(trim($topHeaderLinkColor)) ? 'color:' . $topHeaderLinkColor . ';' : '';

        $topHeaderActiveLinkColor = strlen(trim($topHeaderActiveLinkColor)) ? '&:active { color: ' . $topHeaderActiveLinkColor . '; }' : '';
        $topHeaderHoverLinkColor = strlen(trim($topHeaderHoverLinkColor)) ? '&:hover { color: ' . $topHeaderHoverLinkColor . ' !important; }' : '';

        $topHeaderSubmenuLinkColor = strlen(trim($topHeaderSubmenuLinkColor)) ? 'color:' . $topHeaderSubmenuLinkColor . ' !important;' : '';
        $topHeaderSubmenuHoverLinkColor = strlen(trim($topHeaderSubmenuHoverLinkColor)) ? '&:hover { color: ' . $topHeaderSubmenuHoverLinkColor . ' !important; }' : '';

        $topHeaderBackgroundColor = strlen(trim($topHeaderBackgroundColor)) ? 'background-color:' . $topHeaderBackgroundColor . ' !important;' : '';
        $topHeaderBorderBottomColor = strlen(trim($topHeaderBorderBottomColor)) ? 'border-bottom: 1px solid ' . $topHeaderBorderBottomColor . ';' : '';
        $middleHeaderWidth = strlen(trim($middleHeaderWidth)) ? 'max-width:' . $middleHeaderWidth . ';' : '';
        $middleHeaderBackgroundColor = strlen(trim($middleHeaderBackgroundColor)) ? 'background-color:' . $middleHeaderBackgroundColor . ' !important;' : '';
        $bottomHeaderWidth = strlen(trim($bottomHeaderWidth)) ? 'max-width:' . $bottomHeaderWidth . ';' : '';
        $bottomHeaderPadding = strlen(trim($bottomHeaderPadding)) ? '@media (max-width: ' . $bottomHeaderPadding . '){ padding-right: 15px !important; padding-left: 15px !important; }' : '';
        $bottomHeaderBackgroundColor = strlen(trim($bottomHeaderBackgroundColor)) ? 'background-color:' . $bottomHeaderBackgroundColor . ' !important;' : 'background-color: transparent !important;';
        $bottomHeaderMenuFontSizeL0 = strlen(trim($bottomHeaderMenuFontSizeL0)) ? 'font-size:' . $bottomHeaderMenuFontSizeL0 . ' !important;' : 'font-size: inherit;' ;
        $bottomHeaderMenuFontSizeL1 = strlen(trim($bottomHeaderMenuFontSizeL1)) ? 'font-size:' . $bottomHeaderMenuFontSizeL1 . ' !important;' : 'font-size: inherit;' ;
        $bottomHeaderMenuFontSizeL2 = strlen(trim($bottomHeaderMenuFontSizeL2)) ? 'font-size:' . $bottomHeaderMenuFontSizeL2 . ' !important;' : 'font-size: inherit;' ;
        $bottomHeaderBorderHoverColor = strlen(trim($bottomHeaderHoverLinkColor)) ? 'border-color:' . $bottomHeaderHoverLinkColor . '40 !important;' : '';
        $bottomHeaderLinkColor = strlen(trim($bottomHeaderLinkColor)) ? 'color:' . $bottomHeaderLinkColor . ' !important;' : '';
        $bottomHeaderLinkColorShadow = strlen(trim($bottomHeaderHoverLinkColor)) ? 'text-shadow: 0 0 0 ' . $bottomHeaderHoverLinkColor . ' !important;' : '';
        $bottomHeaderLinkColorHover = strlen(trim($bottomHeaderHoverLinkColor)) ? 'color: ' . $bottomHeaderHoverLinkColor . ' !important;' : '';
        $bottomHeaderHoverLinkColor = strlen(trim($bottomHeaderHoverLinkColor)) ? '&:hover { color: ' . $bottomHeaderHoverLinkColor . ' !important; }' : '';
        $bottomNavigationShadow = strlen(trim($bottomNavigationShadow)) ? '-webkit-box-shadow: ' . $bottomNavigationShadow . '; -moz-box-shadow: ' . $bottomNavigationShadow . '; -o-box-shadow: ' . $bottomNavigationShadow . '; box-shadow: ' . $bottomNavigationShadow . ';' : '';

        $serachOptionsWidth = strlen(trim($serachOptionsWidth)) ? 'width: ' . $serachOptionsWidth . ';' : '';
        $serachOptionsHeight = strlen(trim($serachOptionsHeight)) ? 'height: ' . $serachOptionsHeight . ';' : '';

        if($inputBorders == 0){
            $inputBorders = strlen(trim($inputBorders)) ? 'border:' . $borderWidth . 'px solid ' . $mainSearchElementsColor . '!important;'  : '';

        }else{
            $inputBorders = strlen(trim($inputBorders)) ? 'border:none !important; border-bottom:' . $borderWidth . 'px solid ' . $mainSearchElementsColor . '!important; '  : '';
        }

        $rgba = 'background-color: rgba(' . implode(",", array_values($this->hex2rgb($backgroundColorSearchv2))) . ',' . $backgroundOpacitySearchv2 .' ) !important;';
        $backgroundColorSearchv2 = strlen(trim($backgroundColorSearchv2)) ? $rgba : '';

        $inputBackgroundColor = strlen(trim($inputBackgroundColor)) ? 'background-color:' . $inputBackgroundColor . '!important;'  : '';
        $mainSearchElementsColor = strlen(trim($mainSearchElementsColor)) ? 'color:' . $mainSearchElementsColor . '!important;'   : '';
        $magnifierBackgroundColor = strlen(trim($magnifierBackgroundColor)) ? 'background-color:' . $magnifierBackgroundColor . ';'  : '';
        $searchInputFontSize = strlen(trim($searchInputFontSize)) ? 'font-size:' . $searchInputFontSize . 'px !important;'  : '';

        $defaultFontSettings = $this->_frontendHelper->getDefaultFontSettings($storeId);
        $navFontSize = strlen(trim($defaultFontSettings['font____size__base'])) ? 'font-size: ' . (int) $defaultFontSettings['font____size__base'] . 'px !important;' : '';

        if (!$serachOptionsBorderWidth) {
            $serachOptionsBorderWidth = [];
        } else {
            try {
                $serachOptionsBorderWidthJson = json_decode($serachOptionsBorderWidth);
                /** magento 2.2 removed serialization  */
                if ($serachOptionsBorderWidthJson && ($serachOptionsBorderWidth != $serachOptionsBorderWidthJson)) {
                    $serachOptionsBorderWidth = json_decode($serachOptionsBorderWidth, true);
                    $serachOptionsBorderWidth = $serachOptionsBorderWidth['<%- _id %>'];
                } else {
                    $serachOptionsBorderWidth = unserialize($serachOptionsBorderWidth)['<%- _id %>'];
                }
            } catch (\Exception $ex) {
                $serachOptionsBorderWidth = [];
            }
        }


        $searchOBW = [];
        $true = false;
        foreach ($serachOptionsBorderWidth as $serachOptionsBorderWidth) {
            if ($serachOptionsBorderWidth) {
                $true = true;
            }
            $searchOBW[] .= $serachOptionsBorderWidth;
        }
        $searchOBW = implode(' ', $searchOBW);
        $serachOptionsBorderWidth = strlen(trim($true)) ? 'border-width: ' . $searchOBW . ';' : 'border-width: 0px 0px 1px 0px;';
        $serachOptionsBorderStyle = strlen(trim($serachOptionsBorderStyle)) ? 'border-style: ' . $serachOptionsBorderStyle . ';' : 'border-style: solid;';
        $serachOptionsBorderColor = strlen(trim($serachOptionsBorderColor)) ? 'border-color: ' . $serachOptionsBorderColor . ';' : 'border-color: #000000;';
        $serachOptionsBackground = strlen(trim($serachOptionsBackground)) ? 'background-color: ' . $serachOptionsBackground . ';' : 'background-color: transparent;';
        $serachOptionsColor = strlen(trim($serachOptionsColor)) ? 'color: ' . $serachOptionsColor . ';' : 'color: initial;';
        $serachOptionsPlaceHolderColor = strlen(trim($serachOptionsPlaceHolderColor)) ? 'color: ' . $serachOptionsPlaceHolderColor . '!important;' : 'color: #000000;';
        $serachOptionsFontSize = strlen(trim($serachOptionsFontSize)) ? 'font-size: ' . $serachOptionsFontSize . ';' : 'font-size: 15px;';

        $headerIconSize = strlen(trim($headerIconSize)) ? 'font-size: ' . $headerIconSize . ' !important;' : 'font-size: 16px !important;';
        $headerIconColor = strlen(trim($headerIconColor)) ? 'color: ' . $headerIconColor . ' !important' : 'color: inherit';
        $headerIconHoverColor = strlen(trim($headerIconHoverColor)) ? 'color: ' . $headerIconHoverColor . ' !important' : 'color: inherit';

        $bkColorMobile = (int)$this->_mobileBreakPoint - 1 . 'px';
        $maxBkColorMobile = (int)$this->_mobileBreakPoint . 'px';

        // sticky header
        $stickyHeaderBackgroundColor = $stickyHeaderBackgroundColor && strlen(trim($stickyHeaderBackgroundColor)) ? 'background-color:' . $stickyHeaderBackgroundColor . ' !important;' : 'background-color: #ffffff !important;';
        $stickyHeaderElementsColor = $stickyHeaderElementsColor && strlen(trim($stickyHeaderElementsColor)) ? 'color:' . $stickyHeaderElementsColor . ' !important;' : '';
        $stickyHeaderElementsHoverColor = $stickyHeaderElementsHoverColor && strlen(trim($stickyHeaderElementsHoverColor)) ? 'color:' . $stickyHeaderElementsHoverColor . ' !important;' : '';
        $stickyNavigationBorderColor = $stickyNavigationBorderColor && strlen(trim($stickyNavigationBorderColor)) ? 'border-color:' . $stickyNavigationBorderColor . ' !important;' : '';
        $stickyNavigationBorderHoverColor = $stickyNavigationBorderHoverColor && strlen(trim($stickyNavigationBorderHoverColor)) ? 'border-color:' . $stickyNavigationBorderHoverColor . ' !important;' : '';
        $stickySearchBorderColor = $stickySearchBorderColor && strlen(trim($stickySearchBorderColor)) ? 'border-color:' . $stickySearchBorderColor . ' !important;' : '';
        $stickySearchBackgroundColor = $stickySearchBackgroundColor && strlen(trim($stickySearchBackgroundColor)) ? 'background-color:' . $stickySearchBackgroundColor . ' !important;' : 'background-color: transparent !important;';

        //        Generate Less
        $content .= "
        .page-header-v2 {
	        .customer-welcome {
	            .customer-name {
	                span {
	                    display: none;
	                }
	                &:before {
	                    $headerIconColor;
	                    $headerIconSize;
	                }
	                &:hover {
	                    &:before {
	                        $headerIconHoverColor;
	                    }
                    }
	            }
	        }
	    }
	.page-wrapper .page-header {
        $middleHeaderBackgroundColor
        .block-search input::-webkit-input-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::-moz-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::-ms-placeholder {
            $serachOptionsPlaceHolderColor
        }
        .block-search input::placeholder
        {
            $serachOptionsPlaceHolderColor
        }
        .block-search .action.search:before
        {
            $headerIconColor;
        }
        .block-search .action.search:hover {
            &:before {
                $headerIconHoverColor;
            }
        }
        .panel.wrapper {
            color: initial;
            $topHeaderBorderBottomColor
            $topHeaderBackgroundColor
        }
        .header-global-promo {
            .global-notification-wrapper {
                $globalPromoTextColor
                $globalPromoBackgroundColor
                a.close-global-notification {
                    $globalPromoTextColor
                }
                .wpx-i,
                .wpx-link,
                #buttons a{
                    $globalPromoTextColor
                }
            }
        }
        .panel.header {
            $topHeaderWidth
            .switcher .options {
                div {
                $topHeaderLinkColor
                $topHeaderActiveLinkColor
                $topHeaderHoverLinkColor;
                    &:visited {
                    $topHeaderLinkColor;
                    }
                }
                &:after {
                $topHeaderLinkColor
                $topHeaderActiveLinkColor
                $topHeaderHoverLinkColor;
                }
                ul.switcher-dropdown {
                    li {
                        > a, span {
                        $topHeaderSubmenuLinkColor
                        $topHeaderSubmenuHoverLinkColor;
                            &:visited {
                            $topHeaderLinkColor;
                            }
                        }
                        &:after {
                        $topHeaderSubmenuLinkColor
                        $topHeaderSubmenuHoverLinkColor;
                        }
                    }
                }
            }
            ul.compare {
                li {
                    > a,
                    > a span {
                        &:visited {
                            $topHeaderLinkColor
                        }
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
            }
            ul.header.links {
                li {
                    > a,
                    span {
                        &:visited {
                            $topHeaderLinkColor
                        }
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                    &:after {
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
                .customer-menu {
                    ul.header.links {
                        li {
                            a {
                                $topHeaderSubmenuLinkColor
                                &:visited {
                                    $topHeaderSubmenuLinkColor
                                }
                                $topHeaderSubmenuHoverLinkColor
                            }
                        }
                    }
                }
            }
            .customer-welcome .action.switch:after{
              $topHeaderLinkColor
            }

            .switcher-currency,
            .switcher-language {
                strong {
                    $topHeaderLinkColor
                    $topHeaderActiveLinkColor
                    $topHeaderHoverLinkColor
                    span {
                        $topHeaderLinkColor
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
                .switcher-trigger {
                    &:after {
                        $topHeaderLinkColorImportant
                        $topHeaderActiveLinkColor
                        $topHeaderHoverLinkColor
                    }
                }
            }
        }
    // Middle
    .header-multistore .multistore-desktop .weltpixel_multistore {
        $middleHeaderWidth
    }
    .header.content,
    .header_right {
        $middleHeaderWidth
        $bottomHeaderPadding
        .block-search {
            input {
                $serachOptionsWidth
                $serachOptionsHeight
                $serachOptionsBorderWidth
                $serachOptionsBorderStyle
                $serachOptionsBorderColor
                $serachOptionsBackground
                $serachOptionsColor
                $serachOptionsFontSize
                &:focus{
                     $serachOptionsBorderColor
                }
            }
        }
        .modal{
                $backgroundColorSearchv2
                .close-sec{
                    a{
                        &:before{
                          $mainSearchElementsColor
                        }
                    }
                }
                .actions.wpx-pos-search{
                    button{
                        $magnifierBackgroundColor
                    }
                }
                #search{
                 $inputBackgroundColor
                 $mainSearchElementsColor
                 $inputBorders
                 $searchInputFontSize
                     &::-webkit-input-placeholder {
                        $searchInputFontSize
                     }
                     &::-moz-placeholder{
                        $searchInputFontSize
                     }
                     &:-ms-input-placeholder{
                        $searchInputFontSize
                     }
                     &:-moz-placeholder{
                        $searchInputFontSize
                     }
                }
                @media (max-width: $bkColorMobile) {
                    #search.horizontally-white{
                      &::-webkit-input-placeholder {
                            font-size:18px !important;
                         }
                         &::-moz-placeholder{
                            font-size:18px !important;
                         }
                         &:-ms-input-placeholder{
                            font-size:18px !important;
                         }
                         &:-moz-placeholder{
                            font-size:18px !important;
                         }
                    }
                }

                .action.search:before{
                   $mainSearchElementsColor
                }
        }
    }
    .header.content {
        .nav-toggle {
            &:before {
                $headerIconColor
            }
            &:hover {
                &:before {
                    $headerIconColor
                }
            }
        }
    }
    #switcher-language,
    #switcher-currency {
        ul {
            li {
                a {
                    $topHeaderSubmenuLinkColor
                    &:visited {
                        $topHeaderSubmenuLinkColor
                    }
                    $topHeaderSubmenuHoverLinkColor
                }
            }
        }
    }
    .header.links > li.authorization-link a:before,
    .minicart-wrapper .action.showcart:before,
    .minicart-wrapper .action.showcart.active:before,
    .block-search .action.search:before {
        $headerIconSize
    }
    .block-search {
        &.minisearch-v2 .open-modal-search {
            $headerIconColor;
            $headerIconSize;
            &:hover {
                $headerIconHoverColor;
            }
        }
    }
    .header.links .authorization-link a:before,
	.minicart-wrapper .action.showcart:before {
	    $headerIconColor;
	}
	.header.links .authorization-link a:hover:before,
	.minicart-wrapper .action.showcart:hover:before {
	    $headerIconHoverColor;
	}
	.header.content,
	.header_right {
	    .field.search {
	        label {
	            $headerIconColor;
	        }
	    }
	}
}
body:not(.mobile-nav){
.nav-sections {
    background-color: transparent !important;
    .navigation {
        $bottomHeaderWidth
        $bottomHeaderBackgroundColor
        $bottomHeaderPadding
        ul {
            li.level0 > a {
                $bottomHeaderLinkColor
                &:visited {
                    $bottomHeaderLinkColor
                }
                $bottomHeaderHoverLinkColor
                @media (max-width: $bkColorMobile) {
			        color: #575757 !important;
			    }
            }
            li.parent > a span:nth-child(2),
            li:not(.parent) > a span:first-child {
                $navFontSize
            }
            li.level0 {
                .parent > a {
                    @media (min-width: $this->_mobileBreakPoint) {
                        padding: 8px 20px;
                    }
                }
            }
        }
        ul li.level0 {
            & > a.bold-menu {
                $bottomHeaderLinkColorHover
                &:visited {
                    $bottomHeaderLinkColorHover
                }
            }
            li > a:hover {
                $bottomHeaderLinkColorHover
            }
        }
        ul li.level0:hover > a { $bottomHeaderLinkColorHover }
        @media (max-width: $bkColorMobile) {
	        background-color: inherit !important;
	    }
	    .megamenu {
	        .submenu [data-has-children] {
	            @media (min-width: $this->_mobileBreakPoint) {
	                a {
                        &:hover {
                            span:last-child {
                                $bottomHeaderBorderHoverColor
                            }
                        }
	                }
	            }
	        }
	    }
    }
     @media (min-width: $this->_mobileBreakPoint) {
        .nav-sections-items {
            $bottomHeaderBackgroundColor
        }
        .navigation ul li.level0 > a span:first-child,
        .navigation ul li.level0 > a span:nth-child(2) {
            $bottomHeaderMenuFontSizeL0
        }
        .navigation ul li.level1 > a span:first-child,
        .navigation ul li.level1 > a span:nth-child(2) {
            $bottomHeaderMenuFontSizeL1
        }
        .navigation ul li.level2 > a span:first-child,
        .navigation ul li.level2 > a span:nth-child(2) {
            $bottomHeaderMenuFontSizeL2
        }
    }
    .megamenu a.bold-menu,
    .megamenu a:hover,
    .megamenu.level1:not(.parent):hover,
    .megamenu.level2:hover {
        $bottomHeaderLinkColorHover
        & > a {
            &:hover {
                $bottomHeaderLinkColorShadow
                $bottomHeaderLinkColorHover
            }
        }
    }
    .nav-sections-item-content .navigation {
        @media only screen and (max-width: $maxBkColorMobile) {
	        border-top: 0 none !important;
	    }
    }
    @media (max-width: $bkColorMobile) {
        background-color: white !important;
    }
    .nav-sections-item-content {
        @media (min-width: $bkColorMobile) {
            $bottomNavigationShadow
        }
    }
}
}
// Sticky Header
.page-header.sticky-header,
.page-header.sticky-header-mobile {
    $stickyHeaderBackgroundColor
    .page-header {
        $stickyHeaderBackgroundColor
    }
    .panel.wrapper {
        $stickyHeaderBackgroundColor
    }
    .header.links {
        $stickyHeaderElementsColor
        li > a {
            $stickyHeaderElementsColor
            &:visited {
                $stickyHeaderElementsColor
                &:hover {
                    $stickyHeaderElementsHoverColor
                }
            }
            &:hover {
                $stickyHeaderElementsHoverColor
            }
        }
        li:after {
            $stickyHeaderElementsColor
        }
    }
    .nav-sections {
        .navigation,
        .nav-sections-items {
            background-color: transparent !important;
        }
    }
    .navigation ul li.level0 > a,
    .navigation ul li.level0 > a:visited {
        $stickyHeaderElementsColor
        &:hover {
            $stickyHeaderElementsHoverColor
        }
        li > a,
        li > a:visited {
            $stickyHeaderElementsColor
            &:hover {
                $stickyHeaderElementsHoverColor
            }
        }
    }
    .navigation ul li.level0 > a:hover,
    .navigation ul li.level0 > a.bold-menu {
        $bottomHeaderLinkColorHover
        &:visited {
            $bottomHeaderLinkColorHover
        }
    }
    .navigation ul li.level0.active > a,
    .navigation ul li.level0.has-active > a {
        $stickyNavigationBorderColor
        &:hover {
            $stickyNavigationBorderHoverColor
        }
    }
    .minicart-wrapper {
        .action.showcart {
            &:before {
                $stickyHeaderElementsColor
            }
            &:hover {
                &:before {
                    $stickyHeaderElementsHoverColor
                }
            }
        }
    }
    .block-search {
        .label {
            &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
        }
    }
    .header_right {
        .block-search input::-webkit-input-placeholder { $stickyHeaderElementsColor }
        .block-search input::-moz-placeholder { $stickyHeaderElementsColor }
        .block-search input:-ms-input-placeholder { $stickyHeaderElementsColor }
        .block-search input:-moz-placeholder { $stickyHeaderElementsColor }
        .block-search {
            input {
                $stickyHeaderElementsColor
                $stickySearchBorderColor
                $stickySearchBackgroundColor
            }
            .action.search {
                &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
    }
    .block-search {
        .field.search {
            label {
                $stickyHeaderElementsColor
            }
            .control {
                $stickySearchBackgroundColor
            }
        }
        &.minisearch-v2 .open-modal-search {
            $stickyHeaderElementsColor
            &:hover {
                $stickyHeaderElementsHoverColor
            }
        }
    }
    .header.content {
        .authorization-link,
        .minicart-wrapper {
            a {
                &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    $stickyHeaderElementsHoverColor
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
        .block-search {
            .control {
                $stickySearchBackgroundColor
            }
            input {
                $stickySearchBackgroundColor
                $stickyHeaderElementsColor
                $stickySearchBorderColor
            }
            input:not(.vertically-black):not(.horizontally-white){
                &::-webkit-input-placeholder { $stickyHeaderElementsColor }
                &::-moz-placeholder { $stickyHeaderElementsColor }
                &:-ms-input-placeholder { $stickyHeaderElementsColor }
                &:-moz-placeholder { $stickyHeaderElementsColor }
            }
            .action.search {
                &:before {
                    $stickyHeaderElementsColor
                }
                &:hover {
                    &:before {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
        .nav-toggle {
            &:before {
                $stickyHeaderElementsColor
            }
            &:hover {
                $stickyHeaderElementsHoverColor
                &:before {
                    $stickyHeaderElementsHoverColor
                }
            }
        }
        .modal{
           .modal-content{
              .block-search.wpx-block-search{
                   #search{
                      $inputBackgroundColor;
                   }
              }
           }
        }
    }
    .header.links .authorization-link {
        margin-left: 0;
    }
    .minicart-wrapper .action.showcart {
        &:before {
            $stickyHeaderElementsColor
        }
        &:hover {
            &:before {
                $stickyHeaderElementsHoverColor
            }
        }
    }
}
.page-header.page-header-v4.sticky-header {
    .header.content {
        z-index: 1;
    }
    .wrap{
        .modal-content{
            #search.horizontally-white{
                &::-webkit-input-placeholder { $serachOptionsPlaceHolderColor }
                &::-moz-placeholder { $serachOptionsPlaceHolderColor }
                &:-ms-input-placeholder { $serachOptionsPlaceHolderColor }
                &:-moz-placeholder { $serachOptionsPlaceHolderColor }
            }
        }
    }


    .panel.wrapper {
        background-color: transparent !important;
        #switcher-currency {
            &.switcher {
                .toggle.switcher-trigger {
                    &:after {
                        $stickyHeaderElementsColor
                    }
                    &:hover {
                        $stickyHeaderElementsHoverColor
                        &:after {
                            $stickyHeaderElementsHoverColor
                        }
                    }
                }
                strong {
                    $stickyHeaderElementsColor
                    &:hover {
                        $stickyHeaderElementsHoverColor
                    }
                }
            }
        }
        .panel.header{
            .header.links {
                li {
                    $stickyHeaderElementsColor
                    & > a,
                    & > span {
                        $stickyHeaderElementsColor
                        &:visited {
                            $stickyHeaderElementsColor
                        }
                        &:hover {
                            $stickyHeaderElementsHoverColor
                        }
                    }
                }
            }
            .switcher-currency{
                display: none;
            }
        }
    }
}
body:not(.mobile-nav){
    .page-header.sticky-header,
    .nav-sections.sticky-header {
        $stickyHeaderBackgroundColor
        padding-bottom: 0 !important;
        .nav-sections-item-content {
            $stickyHeaderBackgroundColor
        }
        .navigation {
            $stickyHeaderBackgroundColor
            ul li.level0 > a,
            ul li.level0 > a:visited {
                $stickyHeaderElementsColor
                &:hover {
                    $stickyHeaderElementsHoverColor
                }
            }
        }
    }
}



        ";
        return $content;
    }

    /**
     * Generate the less css content for the logo
     *
     * @param int $storeId
     * @return string
     */
    private function _generateLogoLess($storeId)
    {
        $content = '';

        $logoWidth = (int)$this->_scopeConfig->getValue(
            'design/header/logo_width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $logoHeight = (int)$this->_scopeConfig->getValue(
            'design/header/logo_height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $logoSrc = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $logoRatio = 2.85;
        if ($logoSrc) {
            $logoSrc = $this->_dir->getPath('media') . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . $logoSrc;
            $imgPathArr = explode('.', $logoSrc);
            $imgType = end($imgPathArr);

            if ($imgType != 'svg') {
                list($width, $height) = @getimagesize($logoSrc);
                if ($height) {
                    $logoRatio = $width / $height;
                }
            } else {
                $xml = simplexml_load_file($logoSrc);
                $attr = $xml->attributes();
                if ($attr->width && $attr->height) {
                    $logoRatio = $attr->width / $attr->height;
                }
            }
        }

        $logoImgSizeCss = '';

        if ($logoWidth && !$logoHeight) {
            $logoHeight = 'auto';
        } elseif (!$logoWidth && $logoHeight) {
            $logoWidth = 'auto';
        } elseif (!$logoWidth && !$logoHeight) {
            $logoWidth = 96;
            $logoHeight = 34;
        }

        $logoImgMxWidthCss = "max-width: ${logoWidth}px;";
        $logoImgSizeCss .= is_int($logoWidth) ? "width: ${logoWidth}px;" : "width: ${logoWidth};";
        $logoImgSizeCss .= is_int($logoHeight) ? "height: ${logoHeight}px;" : "height: ${logoHeight};";

        /** This is for the admin image width height proper usage */
        $content .= "
            @media (min-width: $this->_mobileBreakPoint) {
                :root .theme-pearl {
                    .page-wrapper {
                        .page-header {
                            .logo {
                                img {
                                    $logoImgSizeCss
                                }
                            }
                        }
                    }
                }
            }
        ";

        // sticky header logo width and height
        $stickyLogoHeight = 34;
        $stickyLogoWidth = (int) ($stickyLogoHeight * $logoRatio);
        // recalc logo width and height
        if ($stickyLogoWidth > 200) {
            $stickyLogoWidth = 200;
            $stickyLogoHeight = (int) ($stickyLogoWidth / $logoRatio);
        }
        $isCustomLogoSizeOnStickyHeaderEnabled = $this->_helper->customLogoSizeOnStickyHeaderEnabled();
        $stickyHeaderLogoWidth = trim($this->_helper->stickyHeaderLogoWidth());
        $stickyHeaderLogoHeight = trim($this->_helper->stickyHeaderLogoHeight());
        $stickyHeaderLogoTopPosition = trim($this->_helper->stickyHeaderLogoTopPosition());
        $stickyLogoImgSizeCss = $stickyHeaderLogoRecalculated = '';
        $stickyLogoImgSizeCss .= "width: ${stickyLogoWidth}px;";
        $stickyHeaderCustomCss = '';
        if ($isCustomLogoSizeOnStickyHeaderEnabled && strlen($stickyHeaderLogoWidth)) {
            $stickyHeaderLogoRecalculated .= "width:" . $stickyHeaderLogoWidth . ";";
        } elseif ($isCustomLogoSizeOnStickyHeaderEnabled && strlen($stickyHeaderLogoHeight)) {
            $stickyHeaderLogoRecalculated .= "width: auto;";
        } else {
            $stickyHeaderLogoRecalculated .= "width: ${stickyLogoWidth}px;";
        }
        $stickyLogoImgSizeCss .= "height: ${stickyLogoHeight}px;";
        if ($isCustomLogoSizeOnStickyHeaderEnabled && strlen($stickyHeaderLogoHeight)) {
            $parsedHeight = (int)$stickyHeaderLogoHeight;
            if ($parsedHeight > 34) {
                $v4stickyHeaderHeight = 16 + $parsedHeight;
                $v4stickyNavSectionTop = 11 + $parsedHeight;
                $stickyHeaderCustomCss .= "
                :root .theme-pearl {
                    .page-wrapper  {
                        .page-header-v4.sticky-header {
                            height: ${v4stickyHeaderHeight}px;
                        }
                    }
                    .nav-sections-4.sticky-header {
                        top: ${v4stickyNavSectionTop}px;
                    }
                }";
            }
            $stickyHeaderLogoRecalculated .= "height:" . $stickyHeaderLogoHeight . ";";
        } elseif ($isCustomLogoSizeOnStickyHeaderEnabled && strlen($stickyHeaderLogoWidth)) {
            $stickyHeaderLogoRecalculated .= "height: auto;";
        } else {
            $stickyHeaderLogoRecalculated .= "height: ${stickyLogoHeight}px;";
        }

        if ($isCustomLogoSizeOnStickyHeaderEnabled && $stickyHeaderLogoTopPosition) {
            $stickyHeaderCustomCss .=  "
            :root .theme-pearl {
                .page-header.sticky-header:not(.page-header-v4) .logo {
                    top: ${stickyHeaderLogoTopPosition}%;
                }
            }";
        }

        $mainSearchElementsColor = $this->_helper->getMainSearchElementsColor($storeId);
        $mainSearchElementsColor = strlen(trim($mainSearchElementsColor)) ? 'color:' . $mainSearchElementsColor . '!important;'   : '';

        $content .= "
            @media (min-width: $this->_mobileBreakPoint) {
                $stickyHeaderCustomCss
                :root .theme-pearl {
                    .page-wrapper {
                        .page-header.sticky-header {
                             .wrap .modal .block-search .action.search:before{
                              $mainSearchElementsColor
                            }
                            .logo {
                                img {
                                    $stickyHeaderLogoRecalculated
                                }
                            }
                        }
                    }
                }
            }
            @media (max-width: $this->_mobileBreakPoint) {
                :root .theme-pearl {
                    .page-wrapper {
                        .page-header {
                            .logo {
                                z-index: 14;
                                position: relative;
                                img {
                                    $stickyLogoImgSizeCss
                                }
                            }
                        }
                    }
                }
            }
        ";

        $logoMargin = (int) ($stickyLogoHeight / 2) * -1;
        $logoMarginCss = "margin-top: ${logoMargin}px;";

        $stickyNavOffsetLeft = $stickyLogoWidth > 200 ? 220 : $stickyLogoWidth + 20;
        $v3stickyNavOffsetLeft = $stickyLogoWidth > 200 ? 230 : $stickyLogoWidth + 30;

        if ($isCustomLogoSizeOnStickyHeaderEnabled && strlen($stickyHeaderLogoWidth)) {
            $stickyLogoWidthLength = (int)$stickyHeaderLogoWidth;
            $stickyNavOffsetLeft = $stickyLogoWidthLength + 20;
            $v3stickyNavOffsetLeft = $stickyLogoWidthLength + 30;
        }

        $stickyNavOffsetLeftCss = "left: ${stickyNavOffsetLeft}px !important;";
        $v3stickyNavOffsetLeftCss = "margin-left: ${v3stickyNavOffsetLeft}px !important;";


        $content .= "
            :root .theme-pearl {
                .page-wrapper {
                    .page-header.sticky-header:not(.page-header-v4) {
                        .logo {
                            $logoMarginCss
                        }
                        .sections.nav-sections {
                            $stickyNavOffsetLeftCss
                        }
                        &.page-header-v3 {
                            .sections.nav-sections {
                                $v3stickyNavOffsetLeftCss
                            }
                        }
                    }
                }
            }
        ";

        return $content;

    }
}
