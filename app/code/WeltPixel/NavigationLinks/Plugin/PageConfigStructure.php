<?php
namespace WeltPixel\NavigationLinks\Plugin;

class PageConfigStructure {

    /**
     * @var \WeltPixel\NavigationLinks\Helper\Data
     */
    protected $_navigationHelper;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $utilityHelper;

    /**
     * PageConfigStructure constructor.
     * @param \WeltPixel\NavigationLinks\Helper\Data $navigationHelper
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     */
    public function __construct(
        \WeltPixel\NavigationLinks\Helper\Data $navigationHelper,
        \WeltPixel\Backend\Helper\Utility $utilityHelper
    ) {
        $this->_navigationHelper = $navigationHelper;
        $this->utilityHelper = $utilityHelper;
    }

    /**
     * Modify the hardcoded breakpoint for styles-menu.css
     * @param \Magento\Framework\View\Page\Config\Structure $subject
     * @param string $name
     * @param array $attributes
     * @return $this
     */
    public function beforeAddAssets(
        \Magento\Framework\View\Page\Config\Structure
        $subject, $name, $attributes
    )
    {
        $widthThreshold = $this->_navigationHelper->getWidthThreshold();
        $mobileBreakPoint  = $widthThreshold . 'px';
        $desktopBreakPoint  = $widthThreshold + 1 . 'px';

        switch ($name) {
            case 'WeltPixel_NavigationLinks::css/navigation_mobile.css':
                $attributes['media'] = 'screen and (max-width: ' . $mobileBreakPoint . ')';
                if (!$this->_navigationHelper->isEnabled()) {
                    $subject->removeAssets($name);
                }
                break;

            case 'WeltPixel_NavigationLinks::css/navigation_desktop.css':
                $attributes['media'] = 'screen and (min-width: ' . $desktopBreakPoint . ')';
                if (!$this->_navigationHelper->isEnabled()) {
                    $subject->removeAssets($name);
                }
                break;
        }

        return [$name, $attributes];
    }
}