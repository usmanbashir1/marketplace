<?php
 
namespace Cminds\MarketplaceRma\Block\Rma\Create;
use Magento\Framework\View\Element\Template;

/**
 * Class Container
 *
 * @package Cminds\MarketplaceRma\Block\Rma\Create
 */
class Container extends Template
{
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
