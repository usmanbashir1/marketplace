<?php
namespace WeltPixel\FrontendOptions\Plugin;

class ViewPreprocessorAbstract {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \WeltPixel\Command\Model\Storage
     */
    protected $_storage;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $utilityHelper;

    /**
     * @var  \Magento\Framework\App\Request\Http
     */
    protected $_request;


    /**
     * Head constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\Command\Model\Storage $storage
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\Command\Model\Storage $storage,
        \WeltPixel\Backend\Helper\Utility $utilityHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_storeManager = $storeManager;
        $this->_storage = $storage;
        $this->utilityHelper = $utilityHelper;
        $this->_request = $request;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function _getContent($subject, $result) {
        /** Default custom less variables used in theme */
        $replaceString = '@icon_label__font-size: 12px;' . PHP_EOL;
        $replaceString .= '@icon__font-size: 16px;' . PHP_EOL;
        $replaceString .= '@old_price__line-through: 1;' . PHP_EOL;
        $replaceString .= '.line-through (@a) when (@a = 1) {text-decoration: line-through;};' . PHP_EOL;
        $replaceString .= '@breadcrumbs-background: transparent;' . PHP_EOL;
        $replaceString .= '@breadcrumbs__separator-color: inherit;' . PHP_EOL;
        $replaceString .= '@breadcrumbs__font-style: italic;' . PHP_EOL;

        $storeCode = $this->_storage->getData('generation_store_code');
        $isPearlResource = true;

        /** Request directly from url, direct css fetching */
        if (!$storeCode) {
            $resource = $this->_request->getParam('resource', false);
            if (strpos($resource, 'adminhtml/') === 0 ) {
                return $result;
            }
            if ($resource) {
                $storeCode = $this->_storeManager->getStore()->getCode();
                $isPearlResourceNeeded = strpos($resource, 'frontend/Pearl/');
                if ($isPearlResourceNeeded === false) {
                    $isPearlResource = false;
                }
            } else {
                /** Grunt less generation fix */
                if ($subject instanceof \Magento\Framework\View\Asset\PreProcessor\Chain) {
                    $targetPath = $subject->getTargetAssetPath();
                    $pathOptions = explode('/', $targetPath);
                    $isPearlResourceNeeded = strpos($targetPath, 'frontend/Pearl/');
                    if ($isPearlResourceNeeded === false) {
                        $isPearlResource = false;
                    } elseif (isset($pathOptions[5]) && in_array($pathOptions[5], ['styles-m.less', 'styles-l.less'])) {
                        try {
                            $storeThemesLocales = $this->utilityHelper->getStoreThemesLocales();
                            $themePath = $pathOptions['1'] . '/' . $pathOptions[2] . '/' . $pathOptions[3];
                            if (isset($storeThemesLocales[$themePath])) {
                                $storeCode = $storeThemesLocales[$themePath];
                            }
                        } catch (\Exception $ex) {}
                    }
                }
            }
        }

        if ($isPearlResource && $storeCode) {
            if ($this->utilityHelper->isPearlThemeUsed($storeCode)) {
                $replaceString = "@import '../WeltPixel_FrontendOptions/css/source/module/_store_" . $storeCode . "_extend.less';";
            }
        }

        $result = str_replace(
            "@import '../WeltPixel_FrontendOptions/css/source/_extend.less';",
            $replaceString,
            $result
        );

        return $result;
    }
}