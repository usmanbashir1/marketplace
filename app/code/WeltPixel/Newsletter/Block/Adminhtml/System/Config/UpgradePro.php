<?php

namespace WeltPixel\Newsletter\Block\Adminhtml\System\Config;

/**
 * Class UpgradePro
 * @package WeltPixel\Newsletter\Block\Adminhtml\System\Config
 */
class UpgradePro extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<td class="value">';
        $html .= '<a target="_blank" href="https://www.weltpixel.com/enhanced-newsletter-popup-magento-2.html">Upgrade to Pro version</a> to enable this functionality.';
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

}