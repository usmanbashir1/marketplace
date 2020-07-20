<?php

namespace WeltPixel\FrontendOptions\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * FrontendOptionsEditActionControllerSaveObserver observer
 */
class FrontendOptionsEditActionControllerSaveObserver implements ObserverInterface
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
     * var \WeltPixel\FrontendOptions\Helper\Fonts
     */
    protected $_fontHelper;

    /**
     * var \WeltPixel\FrontendOptions\Helper\Data
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
     * @param \WeltPixel\FrontendOptions\Helper\Fonts $fontHelper
     * @param \WeltPixel\FrontendOptions\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader $dirReader
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session\Proxy $session
     */
    public function __construct(
        \WeltPixel\FrontendOptions\Helper\Fonts $fontHelper,
        \WeltPixel\FrontendOptions\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Module\Dir\Reader $dirReader,
        \Magento\Framework\Filesystem\Directory\WriteFactory $writeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session\Proxy $session
    )
    {
        $this->_fontHelper = $fontHelper;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_dirReader = $dirReader;
        $this->_writeFactory = $writeFactory;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_session = $session;
    }

    /**
     * Save color options in file
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_storeCollection = $this->_storeManager->getStores();
        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_FrontendOptions');
        $lessTemplate = $this->_dirReader->getModuleDir('', 'WeltPixel_FrontendOptions') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'storeview_template.less';

        $lessVariables = $this->_getLessVariables();

        $extendLessContent = '// Generated Less from WeltPixel_FrontendOptions' . PHP_EOL;

        foreach ($this->_storeCollection as $store) {
            $extendLessContent .= "@import 'module/_store_" . $store->getData('code') . "_extend.less';" . PHP_EOL;
            $generatedCssDirectoryPath =
                DIRECTORY_SEPARATOR . 'frontend' .
                DIRECTORY_SEPARATOR . 'web' .
                DIRECTORY_SEPARATOR . 'css' .
                DIRECTORY_SEPARATOR . 'source' .
                DIRECTORY_SEPARATOR . 'module' .
                DIRECTORY_SEPARATOR . '_store_' . $store->getData('code') . '_extend.less';

            $frontendOptions = $this->_scopeConfig->getValue('weltpixel_frontend_options', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
            $fontFamilyOptions = $this->_fontHelper->getFontFamilyOptions();
            $content = $this->_generateContent($frontendOptions, $fontFamilyOptions);
            $content .= ".line-through (@a) when (@a = 1) {text-decoration: line-through;}";

            /** Adding also the store view specific infromation from the tamplate less */
            $lessValues = $this->_getLessValues($store);
            $content .= PHP_EOL . PHP_EOL;
            $content .= '// Generated Less from WeltPixel_FrontendOptions StoreView Template' . PHP_EOL;
            $content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplate));


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

        /** Path for the _extend.less file */
        $generatedCssDirectoryPath =
            DIRECTORY_SEPARATOR . 'frontend' .
            DIRECTORY_SEPARATOR . 'web' .
            DIRECTORY_SEPARATOR . 'css' .
            DIRECTORY_SEPARATOR . 'source' .
            DIRECTORY_SEPARATOR . '_extend.less';

        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
        $writer = $this->_writeFactory->create($directoryCode, \Magento\Framework\Filesystem\DriverPool::FILE);
        /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
        $file = $writer->openFile($generatedCssDirectoryPath, 'w');
        try {
            $file->lock();
            try {
                $file->write($extendLessContent);
            } finally {
                $file->unlock();
            }
        } finally {
            $file->close();
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

    }

    /**
     * @deprecated
     * @return void
     */
    protected function _generateStoreViewSpecificLess()
    {
        $content = '/* Generated Less from WeltPixel_FrontendOptions */' . PHP_EOL;

        $lessTemplate = $this->_dirReader->getModuleDir('', 'WeltPixel_FrontendOptions') . DIRECTORY_SEPARATOR .
            'data' . DIRECTORY_SEPARATOR . 'storeview_template.less';

        $lessVariables = $this->_getLessVariables();

        foreach ($this->_storeCollection as $store) {
            $lessValues = $this->_getLessValues($store);
            $content .= str_replace($lessVariables, $lessValues, file_get_contents($lessTemplate));
        }

        $directoryCode = $this->_dirReader->getModuleDir('view', 'WeltPixel_FrontendOptions');

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
     * Generate the less css content for global frontend options
     * ____ in field attribute id must be replaced with -
     * Magento is not allowing - character in id field, only this pattern'[a-zA-Z0-9_]{1,}'
     *
     * @param aray $frontendOptions
     * @param array $fontFamilyOptions
     *
     * @return string
     */
    private function _generateContent($frontendOptions, $fontFamilyOptions)
    {
        $content = '// Generated Less from WeltPixel_FrontendOptions' . PHP_EOL;

        /** Add predefined values for some variables */
        $content .= '@breadcrumbs-background: transparent;' . PHP_EOL;
        $content .= '@breadcrumbs__separator-color: inherit;' . PHP_EOL;

        foreach ($frontendOptions as $groupId => $frontendGroup) {
            if (in_array($groupId, array('section_width'))) {
                continue;
            }
            foreach ($frontendGroup as $id => $frontendValue) {
                if ($id == 'top_image') {
                    // exclude weltpixel_frontend_options/contact_options/top_image option
                    continue;
                }
                //ignore _characterset admin options in frontend generation
                //they are used only in google font url creation
                $characterSetOption = strpos($id, '_characterset');
                if (($characterSetOption === false) && trim(strlen($frontendValue))) {
                    if (in_array($id, $fontFamilyOptions)) {
                        if (!$frontendValue) {
                            continue;
                        } else {
                            $frontendValue = "'" . $frontendValue . "', sans-serif";
                        }
                    }
                    /** add border css to color and px to letter-spacing as well */
                    switch ($id) {
                        case 'button__border' :
                        case 'button__hover__border' :
                            $frontendValue .= ' 1px solid';
                            break;
                        case 'h1__letter____spacing':
                        case 'h2__letter____spacing':
                        case 'h3__letter____spacing':
                        case 'h4__letter____spacing':
                        case 'h5__letter____spacing':
                        case 'h6__letter____spacing':
                        case 'font__letter____spacing':
                        case 'button__letter____spacing':
                            $frontendValue .= 'px';
                            break;
                    }

                    $content .= '@' . str_replace('____', '-', $id) . ': ' . $frontendValue . ';' . PHP_EOL;
                }
            }
        }

        return $content;
    }

    /**
     * @return array
     */
    private function _getLessVariables()
    {
        return array(
            '@storeViewClass',
            '@pageMainWidth',
            '@pageMainPadding',
            '@footerWidth',
            '@rowWidth',
            '@defaultPageWidth',
            '@cmsPageWidth',
            '@productPageWidth',
            '@categoryPageWidth'
        );
    }

    /**
     * @param \Magento\Store\Model\Store
     * @return array
     */
    private function _getLessValues(\Magento\Store\Model\Store $store)
    {
        $storeId = $store->getStoreId();
        $storeCode = $store->getData('code');
        $storeClassName = '.theme-pearl.store-view-' . preg_replace('#[^a-z0-9-_]+#', '-', strtolower($storeCode));

        return array(
            $storeClassName,
            $this->_helper->getPageMainWidth($storeId),
            $this->_helper->getPageMainPadding($storeId),
            $this->_helper->getFooterWidth($storeId),
            $this->_helper->getRowWidth($storeId),
            $this->_helper->getDefaultPageWidth($storeId),
            $this->_helper->getCmsPageWidth($storeId),
            $this->_helper->getProductPageWidth($storeId),
            $this->_helper->getCategoryPageWidth($storeId)
        );
    }
}
