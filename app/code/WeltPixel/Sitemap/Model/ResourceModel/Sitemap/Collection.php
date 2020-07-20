<?php
namespace WeltPixel\Sitemap\Model\ResourceModel\Sitemap;

/**
 * Class Collection
 * @package WeltPixel\Sitemap\Model\ResourceModel\Sitemap
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection 
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Sitemap\Model\Sitemap', 'WeltPixel\Sitemap\Model\ResourceModel\Sitemap');
    }
}
