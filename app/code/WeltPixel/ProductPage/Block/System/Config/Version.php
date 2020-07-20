<?php

namespace WeltPixel\ProductPage\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Version
 * @package WeltPixel\ProductPage\Block\System\Config
 */
class Version extends Field
{

    protected $_template = 'WeltPixel_ProductPage::system/config/versions.phtml';

    /**
     * Version constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element) {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getAdminUrl() {
        return $this->_urlBuilder->getUrl('productpage/version/version');
    }
}