<?php

namespace WeltPixel\GoogleCards\Block;

/**
 * Class Breadcrumbs
 * @package WeltPixel\GoogleCards\Block
 */
class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * Breadcrumbs constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    )
    {
        $this->_catalogSession = $catalogSession;
        $this->_catalogHelper = $catalogHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Breadcrumb data from session
     * @return mixed
     */
    public function getCrumbs()
    {
        $crumbs = $this->_catalogSession->getBreadcrumbData() ? $this->_catalogSession->getBreadcrumbData() : [];

        $crumbsWithLinks  = [];
        foreach ($crumbs as $crumb) {
            if (isset($crumb['link']) && strlen($crumb['link'])) {
                $crumbsWithLinks[] = $crumb;
            }
        }

        return $crumbsWithLinks;
    }

    /**
     * @return \Magento\Framework\View\Element\Template|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->_catalogHelper->getProduct()) {
            $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
        }
    }
}
