<?php
namespace WeltPixel\SpeedOptimization\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use \WeltPixel\Backend\Helper\Utility as UtilityHelper;

/**
 * Class AdvancedBundling
 * @package WeltPixel\SpeedOptimization\Block\System\Config
 */
class AdvancedBundling extends Field
{
    protected $_template = 'WeltPixel_SpeedOptimization::system/config/advanced_bundling_container.phtml';

    /**
     * @var string
     */
    protected $postUrl = null;

    /**
     * @var UtilityHelper
     */
    protected $utilityHelper;


    /**
     * Version constructor.
     * @paramUtilityHelper $utilityHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        UtilityHelper $utilityHelper,
        Context $context,
        array $data = []
    ) {
        $this->utilityHelper = $utilityHelper;
        parent::__construct($context, $data);
    }


    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getUrlForItemsCreation()
    {
        $this->postUrl = $this->_urlBuilder->getUrl('speedoptimization/bundling/create');
        return $this->postUrl;
    }

    /**
     * @return array
     */
    public function getBundlingOptions()
    {
        $themesLocales = $this->utilityHelper->getStoreThemesLocales();
        $result = array_keys($themesLocales);
        return $result;
    }

    /**
     * @return string
     */
    public function getMagentoMode() {
        return $this->_appState->getMode();
    }
}
