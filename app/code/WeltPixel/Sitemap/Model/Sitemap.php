<?php
namespace WeltPixel\Sitemap\Model;

/**
 * Class Sitemap
 * @package WeltPixel\Sitemap\Model
 */
class Sitemap extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'weltixel_sitemap';

    /**
     * @var string
     */
    protected $_cacheTag = 'weltpixel_sitemap';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'weltpixel_sitemap';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Sitemap\Model\ResourceModel\Sitemap');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }
}
