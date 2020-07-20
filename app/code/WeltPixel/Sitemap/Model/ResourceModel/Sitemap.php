<?php
namespace WeltPixel\Sitemap\Model\ResourceModel;

/**
 * Class Sitemap
 * @package WeltPixel\Sitemap\Model\ResourceModel
 */
class Sitemap extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_sitemap', 'id');
    }
}
