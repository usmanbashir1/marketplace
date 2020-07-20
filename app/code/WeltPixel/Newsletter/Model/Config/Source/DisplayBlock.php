<?php

namespace WeltPixel\Newsletter\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
/**
 * Class DisplayBlock
 *
 * @package WeltPixel\Newsletter\Model\Config\Source
 */
class DisplayBlock implements ArrayInterface
{

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $_blockCollectionFactory;

    public function __construct(
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory,
        array $data = []
    ) {
        $this->_blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * Return list of Static Blocks
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $blockOptions = array();
        $collection = $this->_blockCollectionFactory->create();

        foreach($collection as $block) {
            $blockOptions[] = array(
                'value' => $block->getIdentifier(),
                'label' => $block->getTitle()
            );
        }

        return $blockOptions;
    }
}