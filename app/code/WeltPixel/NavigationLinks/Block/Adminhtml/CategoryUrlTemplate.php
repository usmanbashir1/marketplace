<?php

namespace WeltPixel\NavigationLinks\Block\Adminhtml;

class CategoryUrlTemplate extends \Magento\Backend\Block\Template
{
    protected function _toHtml()
    {
        return '<div style="margin-top: -30px; margin-bottom: 30px" class="admin__field admin__field-no-label"><div class="admin__field-control"><div class="admin__field admin__field-option"><span>' .
                "1. Use 'http://' or 'https://' to create external link <br/>2. Use '/' to create link to home page <br/>3. Use '#' to disable link"
            . '</span></div></div></div>';
    }
}
