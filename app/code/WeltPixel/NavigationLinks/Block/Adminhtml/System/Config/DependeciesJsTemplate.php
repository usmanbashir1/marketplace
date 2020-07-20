<?php
namespace WeltPixel\NavigationLinks\Block\Adminhtml\System\Config;

class DependeciesJsTemplate extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'WeltPixel_NavigationLinks::system/config/dependencies_js.phtml';

    /**
     * DependeciesJsTemplate constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->setData('template', $this->_template);
        $this->mmOptionsAllowed();

        parent::_construct();
    }

    /**
     * Mega Menu Options available only for the main categories
     *
     * @return bool
     */
    public function mmOptionsAllowed()
    {
        $currentCategory = $this->_registry->registry('current_category');
        if ($currentCategory->getLevel() == 2) {
            return true;
        }

        return false;
    }
}