<?php

namespace WeltPixel\CustomFooter\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * CustomFooterEditActionControllerSaveObserver observer
 */
class CustomFooterEditActionControllerSaveObserver implements ObserverInterface
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
     * var \WeltPixel\CustomFooter\Helper\Data
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
     * Constructor
     *
     * @param \WeltPixel\CustomFooter\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
     */
    public function __construct(
        \WeltPixel\CustomFooter\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $frontendHelper
    )
    {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_frontendHelper = $frontendHelper;
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
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_CustomFooter');

        foreach ($this->_storeCollection as $store) {

            $generatedCssDirectoryPath = DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'weltpixel_custom_footer_' .
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

        return $this;
    }


    /**
     * Generate the less css content for the footer options
     *
     * @param int $storeId
     * @return string
     */
    private function _generateContent($storeId)
    {
        $content = '/* Generated Less from WeltPixel_CustomFooter */' . PHP_EOL;
        $footerMaxWidth = $this->_frontendHelper->getFooterWidth($storeId);
        $footerMaxWidth = strlen(trim($footerMaxWidth)) ? 'max-width: ' . $footerMaxWidth . ';': 'max-width: inherit;';

        $preFooterMaxWidth = $this->_frontendHelper->getRowWidth($storeId);
        $preFooterMaxWidth = strlen(trim($preFooterMaxWidth)) ? 'max-width: ' . $preFooterMaxWidth . ';': 'max-width: inherit;';

        $preFooterBackgroundColor = $this->_helper->getPreFooterBackgroundColor($storeId);
        $preFooterTextColor = $this->_helper->getPreFooterTextColor($storeId);
        $preFooterIconColor = $this->_helper->getPreFooterIconColor($storeId);
        $footerBackgroundColor = $this->_helper->getFooterBackgroundColor($storeId);
        $footerTextColor = $this->_helper->getFooterTextColor($storeId);
        $footerIconColor = $this->_helper->getFooterIconColor($storeId);

        $preFooterBackgroundColor = strlen(trim($preFooterBackgroundColor)) ? 'background-color: ' . $preFooterBackgroundColor . ';': '';
        $preFooterTextColor = strlen(trim($preFooterTextColor)) ? 'color: ' . $preFooterTextColor . ';' : '';
        $preFooterIconColor = strlen(trim($preFooterIconColor)) ? 'color: ' . $preFooterIconColor . ' !important;' : '';
        $footerBackgroundColor = strlen(trim($footerBackgroundColor)) ? 'background-color: ' . $footerBackgroundColor . ';' : '';
        $footerTextColor = strlen(trim($footerTextColor)) ? 'color: ' . $footerTextColor . ';' : '';
        $footerIconColor = strlen(trim($footerIconColor)) ? 'color: ' . $footerIconColor . ' !important;' : '';

        // Generate Less
        $content .=
            ".theme-pearl {
                .page-wrapper {
                    .page-footer {
                        " . $footerBackgroundColor . "
                        position: relative;
                        z-index: 0;
                        .footer.content,
                        .w {
                            max-width: inherit;
                            " . $footerTextColor . "
                            h4,
                            p,
                            a,
                            .togglet.newsletter,
                            small {
                                " . $footerTextColor . "
                                &:visited,
                                &.footer-title {
                                    " . $footerTextColor . "
                                }
                                i {
                                    " . $footerIconColor . "
                                }
                            }
                            a {
                                &:hover {
                                    i {
                                        color: #FFFFFF !important;
                                    }
                                }
                            }
                            .footer-v1-content,
                            .footer-v2-content,
                            .footer-v3-content,
                            .footer-v4-content {
                                p {
                                    " . $footerTextColor . "
                                }
                                .border-v1 {
                                    small {
                                        " . $footerTextColor . "
                                    }
                                }
                            }
                            .pre-footer {
                                " . $preFooterBackgroundColor . "
                                " . $preFooterTextColor . "
                                p {
                                    " . $preFooterTextColor . "
                                }
                                i {
                                    " . $preFooterIconColor . "
                                }
                                .pre-footer-content {
                                    " . $preFooterMaxWidth . "
                                }
                            }
                        }
                        .footer-v1,
                        .footer-v2,
                        .footer-v3,
                        .footer-v4 {
                            " . $footerMaxWidth . "
                            margin: 0 auto;
                            float: none;
                            " . $footerBackgroundColor . "
                            i {
                                " . $footerIconColor . "
                            }
                        }
                    }
                }
                .w {
                    " . $footerBackgroundColor . "
                    i {
                        " . $footerIconColor . "
                    }
                }
                &.fullpagescroll {
                    .page-wrapper .page-footer { position: fixed; }
                }
            } ";

        return $content;
    }
}
