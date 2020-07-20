<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace WeltPixel\Command\Controller\Adminhtml\Cache;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class GenerateCss extends \Magento\Backend\Controller\Adminhtml\Cache
{

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \WeltPixel\Command\Model\GenerateCss
     */
    protected $generateCss;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  ThemeProviderInterface */
    protected $themeProvider;

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
     * @var \Magento\Framework\View\Asset\MergeService
     */
    protected $mergeService;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $cacheManager;


    /**
     * GenerateCss constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \WeltPixel\Command\Model\GenerateCss $generateCss
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\MergeService $mergeService
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \WeltPixel\Command\Model\GenerateCss $generateCss,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\MergeService $mergeService,
        \Magento\Framework\App\Cache\Manager $cacheManager
    )
    {
        parent::__construct($context, $cacheTypeList, $cacheState, $cacheFrontendPool, $resultPageFactory);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->generateCss = $generateCss;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->_storeManager = $storeManager;
        $this->mergeService = $mergeService;
        $this->cacheManager = $cacheManager;
        $this->_storeCollection = $storeManager->getStores();
    }

    /**
     * Generate the css files from admin button trigger
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getPost();
        $storeCode = $params->get('storeview');
        $cssGenerated = false;

        if (!$storeCode) {
            $this->messageManager->addError(__('Store View is required'));
        } else {

            /** Less generation */
            $generateLessCommand = $this->_objectManager->get('WeltPixel\Command\Console\Command\GenerateLessCommand');
            $observer = $this->_objectManager->get('\Magento\Framework\Event\Observer');
            $generationContainer = $generateLessCommand->getGenerationContainer();
            $successMsg = [];
            $errorMsg = [];

            foreach ($generationContainer as $key => $item) {
                try {
                    $item->execute($observer);
                    $successMsg[] = $key . ' module less was generated successfully for all stores.';
                } catch (\Exception $ex) {
                    $errorMsg[] = $key . ' module less was not generated.' . $ex->getMessage();
                }
            }

            if (count($errorMsg)) {
                $this->messageManager->addError(implode("<br/>", $errorMsg));
            }

            if (count($successMsg)) {
                $this->messageManager->addSuccess(implode("<br/>", $successMsg));
            }
            /** Less generation */

            /** Must generate for all the store views */
            if ($storeCode == '-') {
                foreach ($this->_storeCollection as $store) {
                    $storeCode = $store->getData('code');
                    try {
                        $locale = $this->scopeConfig->getValue(
                            Data::XML_PATH_DEFAULT_LOCALE,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $storeCode
                        );

                        $themeId = $this->scopeConfig->getValue(
                            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $storeCode
                        );

                        $theme = $this->themeProvider->getThemeById($themeId);
                        $themePath = $theme->getThemePath();

                        $isPearlTheme = $this->_validatePearlTheme($theme);
                        if ($isPearlTheme) {
                            $this->generateCss->processContent($themePath, $locale, $storeCode);
                            $cssGenerated = true;
                            $this->messageManager->addSuccess(__('Css generation finalized for storeview code: ') . $storeCode);
                        }
                    } catch (\Exception $ex) {
                        $this->messageManager->addError($ex->getMessage() . ' : ' . $storeCode);
                    }
                }

            } else {
                /** Css Generation for only one store  */
                try {
                    $locale = $this->scopeConfig->getValue(
                        Data::XML_PATH_DEFAULT_LOCALE,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeCode
                    );

                    $themeId = $this->scopeConfig->getValue(
                        \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeCode
                    );

                    $theme = $this->themeProvider->getThemeById($themeId);
                    $themePath = $theme->getThemePath();

                    $isPearlTheme = $this->_validatePearlTheme($theme);
                    if ($isPearlTheme) {
                        $this->generateCss->processContent($themePath, $locale, $storeCode);
                        $cssGenerated = true;
                        $this->messageManager->addSuccess(__('Css generation finalized for storeview code: ') . $storeCode);
                    } else {
                        $this->messageManager->addNotice(__('Css generation works only for Pearl theme or subtheme.'));
                    }

                } catch (\Exception $ex) {
                    $this->messageManager->addError($ex->getMessage());
                }
                /** Css Generation for only one store */
            }

            if ($cssGenerated) {
                $this->cleanMergedCss();
            }
        }


        $this->_session->unsWeltPixelCssRegeneration();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('adminhtml/*');
    }

    /**
     * @param \Magento\Theme\Model\Theme $theme
     * @return bool
     */
    protected function _validatePearlTheme($theme)
    {
        $pearlThemePath = 'Pearl/weltpixel';
        do {
            if ($theme->getThemePath() == $pearlThemePath) {
                return true;
            }
            $theme = $theme->getParentTheme();
        } while ($theme);

        return false;

    }

    /**
     * Clean JS/css files cache
     * and refresh the fullpage cache
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function cleanMergedCss() {
        try {
            $this->mergeService->cleanMergedJsCss();
            $this->_eventManager->dispatch('clean_media_cache_after');
            $this->cacheManager->clean(['full_page']);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('An error occurred while clearing the JavaScript/CSS cache.'));
        }

    }
}
