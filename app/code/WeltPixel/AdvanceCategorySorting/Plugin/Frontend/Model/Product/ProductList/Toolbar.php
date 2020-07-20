<?php

namespace WeltPixel\AdvanceCategorySorting\Plugin\Frontend\Model\Product\ProductList;

class Toolbar
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \WeltPixel\AdvanceCategorySorting\Helper\Data
     */
    protected $_helper;

    /**
     * Toolbar constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \WeltPixel\AdvanceCategorySorting\Helper\Data $helper
    )
    {
        $this->request = $request;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $subject
     * @param $result
     * @return mixed|null|string
     */
    public function afterGetOrder(\Magento\Catalog\Model\Product\ProductList\Toolbar $subject, $result)
    {
        if ($this->_helper->getConfigValue('general', 'enable')) {
            $order = null;
            if ($this->request->getParam($subject::ORDER_PARAM_NAME)) {
                $order = $this->request->getParam($subject::ORDER_PARAM_NAME);
            }

            if ($order && $this->request->getParam($subject::DIRECTION_PARAM_NAME)) {
                $order .= '~' . $this->request->getParam($subject::DIRECTION_PARAM_NAME);
            }

            return $order;
        }

        return $result;
    }
}