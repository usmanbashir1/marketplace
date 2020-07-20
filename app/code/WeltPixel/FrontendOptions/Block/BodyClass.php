<?php
namespace WeltPixel\FrontendOptions\Block;

class BodyClass extends \Magento\Backend\Block\AbstractBlock {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_page;

    /**
     * Init constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     * @param \Magento\Framework\View\Page\Config $page
     */
    public function __construct(\Magento\Backend\Block\Context $context, array $data = [],
        \Magento\Framework\View\Page\Config $page,
        \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        parent::__construct($context, $data);
        $this->_page = $page;
        $this->_storeManager = $storeManager;

        $store = $this->_storeManager->getStore();
        $storeCode = $store->getData('code');

        $page->addBodyClass('store-view-' . $storeCode);
    }
}
