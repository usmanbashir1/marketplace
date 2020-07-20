<?php
namespace WeltPixel\CategoryPage\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Helper\Data;

class AddUpdateHandlesObserver implements ObserverInterface
{      
    /**
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * Catalog data
     *
     * @var Data
     */
    protected $_catalogData = null;
    
    const XML_PATH_CATEGORYPAGE_DISPLAY_SWATCHES = 'weltpixel_category_page/general/display_swatches';
    const XML_PATH_CATEGORYPAGE_DISPLAY_BREADCRUMBS = 'weltpixel_category_page/general/remove_breadcrumbs';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $registry
     * @param Data $catalogData
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Registry $registry,
                                Data $catalogData,
                                \Magento\Framework\View\Page\Config $pageConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->catalogData = $catalogData;
        $this->pageConfig = $pageConfig;
    }
    
    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');

        /** Apply only on pages where page is rendered */
        $currentHandles = $layout->getUpdate()->getHandles();
        if (!in_array('default', $currentHandles)) {
            return $this;
        }

        $fullActionName = $observer->getData('full_action_name');

        if ($fullActionName != 'catalog_category_view') {
            return $this;
        }

        $displaySwatches = $this->scopeConfig->getValue(self::XML_PATH_CATEGORYPAGE_DISPLAY_SWATCHES,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $displayBreadcrumbs = $this->scopeConfig->getValue(self::XML_PATH_CATEGORYPAGE_DISPLAY_BREADCRUMBS,  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$displaySwatches) {
            $layout->getUpdate()->addHandle('weltpixel_categorypage_removeswatch');
        }

        $categoryData = [];
        if ($this->registry->registry('current_category')) {
            $categoryData = $this->registry->registry('current_category')->getData();
        }

        $hideTitle = isset($categoryData['weltpixel_hide_title']) ? $categoryData['weltpixel_hide_title'] : 0;
        $hideBreadcrumbs = isset($categoryData['weltpixel_hide_breadcrumbs']) ? $categoryData['weltpixel_hide_breadcrumbs'] : '0';

        if ($hideTitle) {
            $layout->getUpdate()->addHandle('weltpixel_categorypage_removetitle');
        }
        if ($displayBreadcrumbs || $hideBreadcrumbs) {
            $layout->getUpdate()->addHandle('weltpixel_categorypage_removebreadcrumb');
            $title = [];
            $path = $this->catalogData->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $title[] = $breadcrumb['label'];
            }

            $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($title)));
        }

        return $this;
    }

    public function getTitleSeparator($store = null)
    {
        $separator = (string)$this->scopeConfig->getValue('catalog/seo/title_separator', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return ' ' . $separator . ' ';
    }
}
