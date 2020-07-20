<?php

namespace WeltPixel\Multistore\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\UrlCoder;
use Magento\Store\Block\Switcher;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use WeltPixel\Multistore\Helper\Data as MultiStoreHelper;

class BlockSwitcher
{
    /**
     * @var MultiStoreHelper
     */
    protected $_helper;

    /**
     * @var UrlCoder
     */
    protected $_urlCoder;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * PageConfigStructure constructor.
     * @param MultiStoreHelper $helper
     * @param UrlCoder $urlCoder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        MultiStoreHelper $helper,
        UrlCoder $urlCoder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_helper = $helper;
        $this->_urlCoder = $urlCoder;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param Switcher $subject
     * @param $result
     * @return mixed
     */
    public function afterGetTargetStorePostData(
        Switcher $subject,
        $result
    ) {
        if (!$this->_helper->redirectToHomePage()) {
            return $result;
        }

        $decodedResult = json_decode($result, true);

        if (isset($decodedResult['data'][ActionInterface::PARAM_NAME_URL_ENCODED])) {
            $targetUrl = $this->_urlCoder->decode($decodedResult['data'][ActionInterface::PARAM_NAME_URL_ENCODED]);
            $urlOptions = parse_url($targetUrl);
            $newTargetUrl = $urlOptions['scheme'] . '://' . $urlOptions['host'] . '/';

            if ((bool)$this->_scopeConfig->getValue(Store::XML_PATH_STORE_IN_URL, ScopeInterface::SCOPE_STORE)) {
                $urlPathOptions = explode('/', $urlOptions['path']);
                $newTargetUrl.= $urlPathOptions[1] . '/';
            }

            $decodedResult['data'][ActionInterface::PARAM_NAME_URL_ENCODED] = $this->_urlCoder->encode($newTargetUrl);
            $result = json_encode($decodedResult);
        }

        return $result;
    }
}
