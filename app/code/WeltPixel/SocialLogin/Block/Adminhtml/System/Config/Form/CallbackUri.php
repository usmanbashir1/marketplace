<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block\Adminhtml\System\Config\Form;

/**
 * Class CallbackUri
 * @package WeltPixel\SocialLogin\Block\Adminhtml\System\Config\Form
 */
class CallbackUri extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Data
     */
    protected $slHelper;

    public function __construct(
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->slHelper = $slHelper;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $providerName = str_replace(['weltpixel_sociallogin_', '_callback'], '', $element->getHtmlId());
        $urls = $this->slHelper->getCallbackUri($providerName, true);
        $htmlToReturn = '';
        foreach($urls as $url) {
            $htmlToReturn .= '<input id="'. $element->getHtmlId() .'" type="text" name="" value="'. $url .'" class="input-text" readonly="true" disabled="false" /><br/><br/>';
        }
        return $htmlToReturn;
    }
}