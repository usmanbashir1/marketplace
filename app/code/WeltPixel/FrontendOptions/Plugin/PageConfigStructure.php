<?php
namespace WeltPixel\FrontendOptions\Plugin;

class PageConfigStructure
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * var \WeltPixel\FrontendOptions\Helper\Data
     */
    protected $_helper;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $utilityHelper;

    /**
     * Head constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \WeltPixel\FrontendOptions\Helper\Data $helper
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \WeltPixel\FrontendOptions\Helper\Data $helper,
        \WeltPixel\Backend\Helper\Utility $utilityHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->utilityHelper = $utilityHelper;
    }

    /**
     * Modify the hardcoded breakpoint for styles-l.css
     * @param \Magento\Framework\View\Page\Config\Structure $subject
     * @param string $name
     * @param array $attributes
     * @return $this
     */
    public function beforeAddAssets(
        \Magento\Framework\View\Page\Config\Structure $subject,
        $name,
        $attributes
    ) {
        /** Add the store specific css just for stores where theme is used */
        if ($this->utilityHelper->isPearlThemeUsed()) {
            if ($name == 'css/styles-l.css') {
                $attributes['media'] = 'screen and (min-width: ' . $this->_helper->getMobileTreshold() . ')';
            }
        }

        return [$name, $attributes];
    }

    /**
     * @param \Magento\Framework\View\Page\Config\Structure $subject
     * @param string[] $result
     * @return  string[]
     */
    public function afterGetMetadata(\Magento\Framework\View\Page\Config\Structure $subject, $result)
    {
        $result['viewport'] = 'width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no';
        return $result;
    }
}
