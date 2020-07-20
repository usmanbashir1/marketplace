<?php
 
namespace Cminds\MarketplaceRma\Block\Rma\View;

use Magento\Sales\Model\Order;

/**
 * Class Container
 *
 * @package Cminds\MarketplaceRma\Block\Rma\View
 */
class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * @var null|Order
     */
    private $order;

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
        
        return $this;
    }

    /**
     * Get order.
     *
     * @return mixed
     */
    private function getOrder()
    {
        return $this->order;
    }

    /**
     * Get child html.
     *
     * @param string $alias
     * @param bool $useCache
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildHtml($alias = '', $useCache = false)
    {
        $layout = $this->getLayout();
        if ($layout) {
            $name = $this->getNameInLayout();
            foreach ($layout->getChildBlocks($name) as $child) {
                $child->setOrder($this->getOrder());
            }
        }

        return parent::getChildHtml($alias, $useCache);
    }
}
