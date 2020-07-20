<?php

namespace WeltPixel\QuickCart\Plugin\Checkout\CustomerData;

use WeltPixel\QuickCart\Helper\Data as QuickCartHelper;

class Cart
{
    /**
     * @var QuickCartHelper
     */
    protected $quickCartHelper;

    /**
     * @param QuickCartHelper $quickCartHelper
     */
    public function __construct(
        QuickCartHelper $quickCartHelper
    ) {
        $this->quickCartHelper = $quickCartHelper;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        if (!$this->quickCartHelper->quicartIsEnabled()) {
            return $result;
        }

        $quickCartMessageEnabled = false;
        $quickCartMessageContent = '';
        if ($this->quickCartHelper->isQuickCartMessageEnabled()) {
            $quickCartMessageEnabled = true;
            $quickCartMessageContent = $this->quickCartHelper->getQuickCartMessageContentForDisplay();
        }

        $result['weltpixel_quickcart_message_enabled'] = $quickCartMessageEnabled;
        $result['weltpixel_quickcart_message_content'] = $quickCartMessageContent;

        return $result;
    }
}
