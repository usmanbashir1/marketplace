<?php
namespace WeltPixel\DesignElements\Block;

class Init extends \Magento\Backend\Block\AbstractBlock {

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $page;

    /**
     * Init constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     * @param \Magento\Framework\View\Page\Config $page
     */
    public function __construct(\Magento\Backend\Block\Context $context, array $data = [], \Magento\Framework\View\Page\Config $page)
    {
        parent::__construct($context, $data);
        $this->page = $page;
        $pageLayout = $this->page->getPageLayout();
        if ($pageLayout == 'fullscreen') {
            $page->addBodyClass('page-layout-1column');
        }
    }
}
