<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_Multistore
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\Multistore\Block\Adminhtml\System\Config\Form;

/**
 * Class IsPearl
 * @package WeltPixel\SocialLogin\Block\Adminhtml\System\Config\Form
 */
class IsPearl extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \WeltPixel\Backend\Helper\Utility;
     */
    protected $utilityHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \WeltPixel\Backend\Helper\Utility $utilityHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->utilityHelper = $utilityHelper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $isPearl = 0;
        if ($this->utilityHelper->isPearlThemeUsed()) {
            $isPearl = 1;
        }
        $htmlToReturn = '<input id="weltpixel_multistore_general_is_pearl" name="groups[general][fields][is_pearl][value]" value="'.$isPearl.'" data-ui-id="text-groups-general-fields-is_pearl-value"  class="input-text" type="hidden" style="">';

        return $htmlToReturn;
    }
}