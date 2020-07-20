<?php

namespace WeltPixel\Newsletter\Plugin;

class HttpContext
{

    /**
     * GoogleTagManager context
     */
    const CONTEXT_NEWSLETTER_DEVICE = 'weltpixel_newsletter_device';

    /**
     * @var \WeltPixel\Newsletter\Helper\Data
     */
    protected $helper;

    /** @var \WeltPixel\MobileDetect\Helper\Data */
    protected $mobileDetectHelper;


    /**
     * @param \WeltPixel\Newsletter\Helper\Data $helper
     * @param \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper
     */
    public function __construct(
        \WeltPixel\Newsletter\Helper\Data $helper,
        \WeltPixel\MobileDetect\Helper\Data $mobileDetectHelper
    )
    {
        $this->helper = $helper;
        $this->mobileDetectHelper = $mobileDetectHelper;
    }

    /**
     * @param \Magento\Framework\App\Http\Context $subject
     * @return null
     */
    public function beforeGetVaryString(
        \Magento\Framework\App\Http\Context $subject
    ) {
        if ($this->helper->isEnabled() && $this->helper->displayOnMobile() == 0) {
            $isMobileDevice = $this->mobileDetectHelper->isMobile();
            $subject->setValue(
                self::CONTEXT_NEWSLETTER_DEVICE,
                $isMobileDevice,
                false
            );
        }
        return null;
    }
}