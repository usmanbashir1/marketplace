<?php

namespace WeltPixel\Command\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

/**
 * GenerateCssAfterStaticFilesCleanObserver observer
 */
class GenerateCssAfterStaticFilesCleanObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

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
     * @var \Magento\Backend\Model\Session\Proxy
     */
    protected $_session;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $_utilityHelper;

    /** @var  ThemeProviderInterface */
    protected $_themeProvider;

    /**
     * @var \WeltPixel\Command\Model\GenerateCss
     */
    protected $_generateCss;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     * @param \WeltPixel\Command\Model\GenerateCss $generateCss
     * @param ThemeProviderInterface $themeProvider
     * @param \Magento\Backend\Model\Session\Proxy $session
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \WeltPixel\Backend\Helper\Utility $utilityHelper,
        \WeltPixel\Command\Model\GenerateCss $generateCss,
        ThemeProviderInterface $themeProvider,
        \Magento\Backend\Model\Session\Proxy $session
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_storeCollection = $this->_storeManager->getStores();
        $this->_messageManager = $messageManager;
        $this->_utilityHelper = $utilityHelper;
        $this->_generateCss = $generateCss;
        $this->_themeProvider = $themeProvider;
        $this->_session = $session;
    }

    /**
     * ReGenerate the less, css after static files are flushed from admin
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** Less generation */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $generateLessCommand = $objectManager->get('WeltPixel\Command\Console\Command\GenerateLessCommand');
        $observer = $objectManager->get('\Magento\Framework\Event\Observer');
        $generationContainer = $generateLessCommand->getGenerationContainer();
        $errorMsg = [];

        foreach ($generationContainer as $key => $item) {
            try {
                $item->execute($observer);
            } catch (\Exception $ex) {
                $errorMsg[] = $key . ' module less was not generated.' . $ex->getMessage();
            }
        }

        if (count($errorMsg)) {
            $this->_messageManager->addError(implode("<br/>", $errorMsg));
        }
        /** Less generation */


        foreach ($this->_storeCollection as $store) {
            $storeCode = $store->getData('code');
            try {
                $locale = $this->_scopeConfig->getValue(
                    Data::XML_PATH_DEFAULT_LOCALE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeCode
                );

                $themeId = $this->_scopeConfig->getValue(
                    \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeCode
                );

                $theme = $this->_themeProvider->getThemeById($themeId);
                $themePath = $theme->getThemePath();

                $isPearlTheme = $this->_utilityHelper->isPearlThemeUsed($storeCode);
                if ($isPearlTheme) {
                    $this->_generateCss->processContent($themePath, $locale, $storeCode);
                }
            } catch (\Exception $ex) {
                $this->_messageManager->addError($ex->getMessage() . ' : ' . $storeCode);
            }
        }

        $this->_session->unsWeltPixelCssRegeneration();

        return $this;
    }
}
