<?php

namespace WeltPixel\FrontendOptions\Block\Head;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\FrontendOptions\Helper\Data as DataHelper;
use WeltPixel\FrontendOptions\Helper\Fonts as FontsHelper;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fonts extends Template
{

    /**
     * @var FontsHelper
     */
    protected $_fontsHelper;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @param FontsHelper $_fontsHelper
     * @param DataHelper $_dataHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        FontsHelper $_fontsHelper,
        DataHelper $_dataHelper,
        Context $context,
        array $data = []
    ) {
        $this->_fontsHelper = $_fontsHelper;
        $this->_dataHelper = $_dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get the google font url to import in the head section
     * @return boolean|string
     */
    public function getGoogleFonts()
    {
        $googleFonts = $this->_fontsHelper->getGoogleFonts();
        return $googleFonts;
    }

    /**
     * @return array
     */
    public function getAsyncFontFamilyOptions()
    {
        $asyncFontFamiliyOptions = $this->_fontsHelper->getAsyncFontFamilyOptions();
        return $asyncFontFamiliyOptions;
    }

    /**
     * @return boolean
     */
    public function isAsyncFontLoadEnabled()
    {
        return $this->_dataHelper->getLoadGoogleFontAsyncrounously();
    }

}
