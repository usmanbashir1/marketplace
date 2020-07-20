<?php

namespace WeltPixel\SpeedOptimization\Plugin;

use Magento\Framework\HTTP\Header as HttpHeader;
use WeltPixel\SpeedOptimization\Helper\Data as SpeedHelper;

class HttpContext
{

    /**
     * GoogleTagManager context
     */
    const CONTEXT_SPEEDOPTIMIZATION_PRELOADSUPPORT = 'wp_preloadsupport';

    /**
     * @var HttpHeader
     */
    protected $httpHeader;

    /**
     * @var SpeedHelper
     */
    protected $speedHelper;

    /**
     * @param SpeedHelper $speedHelper
     * @param HttpHeader $httpHeader
     */
    public function __construct(
        SpeedHelper $speedHelper,
        HttpHeader $httpHeader
    )
    {
        $this->speedHelper = $speedHelper;
        $this->httpHeader = $httpHeader;
    }

    /**
     * @param \Magento\Framework\App\Http\Context $subject
     * @return null
     */
    public function beforeGetVaryString(
        \Magento\Framework\App\Http\Context $subject
    )
    {
        if ($this->speedHelper->isCssPreloadEnabled()) {
            $isPreloadNotSupported = $this->speedHelper->checkifBrowserPreloadNotSupported();
            $subject->setValue(
                self::CONTEXT_SPEEDOPTIMIZATION_PRELOADSUPPORT,
                $isPreloadNotSupported,
                false
            );
        }
        return null;
    }
}