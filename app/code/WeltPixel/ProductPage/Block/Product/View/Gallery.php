<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_{Module}
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */


namespace WeltPixel\ProductPage\Block\Product\View;

use Magento\Framework\Json\EncoderInterface;
use WeltPixel\ProductPage\Helper\Data;
use WeltPixel\MobileDetect\Helper\Data as MobileHelper;
use Magento\Framework\App\Request\Http;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
    /**
     * page versions, for which gallery is not applied
     */
    const PAGE_VERSION_NO_GALLERY = [2, 4];

    /**
     * @var Http
     */
    protected $request;

    const WP_QUICKVIEW_REQUEST_ROUTE = 'weltpixel_quickview';

    /**
     * Gallery constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        Http $request,
        array $data = []
    )
    {
        parent::__construct($context, $arrayUtils, $jsonEncoder, $data);
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isQuickViewRequest() {
        $route = $this->request->getRouteName();
        if($route == self::WP_QUICKVIEW_REQUEST_ROUTE) {
            return true;
        }
        return false;
    }

}