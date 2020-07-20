<?php

namespace WeltPixel\SpeedOptimization\Observer;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * CustomHeaderEditActionControllerSaveObserver observer
 */
class AdminOptionsEditActionControllerSaveObserver implements ObserverInterface
{

    const PATH_JAVASCRIPT_ADVANCED_BUNDLING = 'weltpixel_speedoptimization/js_optimization/enable_advanced_bundling';
    const PATH_JAVASCRIPT_MERGE = 'dev/js/merge_files';
    const PATH_JAVASCRIPT_BUNDLE = 'dev/js/enable_js_bundling';
    const PATH_JAVASCRIPT_MINIFY = 'dev/js/minify_files';

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * AdminOptionsEditActionControllerSaveObserver constructor.
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $website = $observer->getWebsite();
        $store = $observer->getStore();
        $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeId = 0;

        if ($website) {
            $scope = ScopeInterface::SCOPE_WEBSITES;
            $scopeId = $website;
        }
        if ($store) {
            $scope = ScopeInterface::SCOPE_STORES;
            $scopeId = $store;
        }

        $advancedBundlingEnable = $this->scopeConfig->getValue(self::PATH_JAVASCRIPT_ADVANCED_BUNDLING, $scope);
        if ($advancedBundlingEnable) {
            $this->configWriter->save(self::PATH_JAVASCRIPT_BUNDLE,0, $scope, $scopeId);
            $this->configWriter->save(self::PATH_JAVASCRIPT_MERGE,0, $scope, $scopeId);
            $this->configWriter->save(self::PATH_JAVASCRIPT_MINIFY,0, $scope, $scopeId);
        }

        return $this;
    }
}
