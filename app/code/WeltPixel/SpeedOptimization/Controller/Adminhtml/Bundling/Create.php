<?php
namespace WeltPixel\SpeedOptimization\Controller\Adminhtml\Bundling;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\ScopeInterface;
use WeltPixel\SpeedOptimization\Helper\Bundling as BundlingHelper;

/**
 * Class \WeltPixel\SpeedOptimization\Controller\Adminhtml\Items\Create
 */
class Create extends Action
{
    const PATH_JAVASCRIPT_MERGE = 'dev/js/merge_files';
    const PATH_JAVASCRIPT_BUNDLE = 'dev/js/enable_js_bundling';
    const PATH_JAVASCRIPT_MINIFY = 'dev/js/minify_files';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var BundlingHelper
     */
    protected $bundlingHelper;

    /**
     * Version constructor.
     *
     * @param Context $context
     * @param BundlingHelper $bundlingHelper
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        BundlingHelper $bundlingHelper,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        JsonFactory $resultJsonFactory
    ) {
        $this->bundlingHelper = $bundlingHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = [];
        $params = $this->getRequest()->getParams();
        $step = $params['step'];
        switch ($step) {
            case '1':
                $this->_parseStep1($params);
                break;
            case '2':
                $result = $this->_parseStep2($params);
                break;
            case '3':
                $result['msg'] = $this->_parseStep3($params);
                break;
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);
        return $resultJson;
    }

    protected function _parseStep1($params)
    {
        $store = (isset($params['store'])) ? $params['store'] : 0;
        $website = (isset($params['website'])) ? $params['website'] : 0;
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

        $this->configWriter->save(self::PATH_JAVASCRIPT_BUNDLE, 0, $scope, $scopeId);
        $this->configWriter->save(self::PATH_JAVASCRIPT_MERGE, 0, $scope, $scopeId);
        $this->configWriter->save(self::PATH_JAVASCRIPT_MINIFY, 0, $scope, $scopeId);
    }

    /**
     * @param $params
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _parseStep2($params)
    {
        $result = [];
        $error = false;
        $frontendPath = $this->bundlingHelper->getFrontendPath();
        $themeLocales = $params['themelocales'];
        foreach ($themeLocales as $path) {
            $sourceDir  = $frontendPath . $path;
            $destinationDir  = $sourceDir . '_tmp';
            if (!file_exists($sourceDir)) {
                $result[] = __('There was no static content found for: ' . $path);
                $error = true;
                continue;
            }
            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0775);
                $this->bundlingHelper->copyDirectory($sourceDir, $destinationDir);
                $result[] = __('Prepared content for: ' . $path);
            } else {
                $result[] = __('The content was already created for: ' . $path);
                continue;
            }
        }

        if ($error) {
            $result[] = '<br/>' . __('Make sure your site is on production mode and static content is properly deployed.');
        }

        return [
            'msg' => $result,
            'error' => $error
        ];
    }

    /**
     * @param $params
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _parseStep3($params)
    {
        $result = [];

        $themeLocales = $params['themelocales'];
        $generationOptions = $this->bundlingHelper->getGenerationOptions($themeLocales);

        foreach ($generationOptions as $options) {
            if (file_exists($options['destinationPath'])) {
                $result[] = __("The %1 file was already added.", $options['buildFileName']) . '<br/>';
            } else {
                @copy($options['buildPath'], $options['destinationPath']);
                $result[] = __("The %1 file is ready.", $options['buildFileName']) . '<br/>';
            }
            $result = array_values(array_unique($result));
        }

        $result[] = __('Execute the following CLI SSH commands from your project\'s root path. Note that require.js needs to be installed for the commands to work. More details about the requirements can be found in the Speed Optimization module documentation.') . '<br/>';
        $commandIterator = 1;
        foreach ($themeLocales as $path) {
            $result[] = __('Command') . ' #' . $commandIterator . '<br/>';
            $result[] = '<b>' . 'node_modules/requirejs/bin/r.js -o pub/static/' . $generationOptions[$path]['buildFileName'] . ' baseUrl=pub/static/frontend/'
                . $path . '_tmp dir=pub/static/frontend/' . $path . '</b><br/>';
            $commandIterator +=1;
        }

        $result[] = __("After the commands have been executed, the Advanced Bundling proces is complete. Flush all Caches and reload the frontend.");

        return $result;
    }
}
