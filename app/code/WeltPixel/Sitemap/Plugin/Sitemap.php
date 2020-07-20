<?php
namespace WeltPixel\Sitemap\Plugin;

/**
 * Class Sitemap
 * @package WeltPixel\Sitemap\Plugin
 */
class Sitemap
{

    /**
     * @var \WeltPixel\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory
     */
    protected $_sitemapCollectionFactory;

    /**
     * Sitemap constructor.
     * @param \WeltPixel\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollectionFactory
     */
    public function __construct(
        \WeltPixel\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollectionFactory)
    {
        $this->_sitemapCollectionFactory = $sitemapCollectionFactory;
    }

    /**
     * @param \Magento\Sitemap\Model\Sitemap $subject
     */
    public function beforeGenerateXml(\Magento\Sitemap\Model\Sitemap $subject) {

        $storeId = $subject->getStoreId();

        $collection = $this->_sitemapCollectionFactory->create();
        $collection->addFieldToFilter('store_id', array('in' => array(0, $storeId)));

        foreach ($collection->getItems() as $sitemap) {
            $row = $this->_prepareSitemap($sitemap->getData());
            $subject->addSitemapItem($row);
        }

    }

    /**
     * @param array $data
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSitemap(array $data)
    {
        $sitemap = new \Magento\Framework\DataObject();
        $sitemap->setId($data['id']);
        $sitemap->setUrl($data['url']);
        $sitemap->setUpdatedAt($data['updated_at']);
        $sitemap->setPriority($data['priority']);
        $sitemap->setChangeFrequency($data['changefreq']);


        $sitemapRow = new \Magento\Framework\DataObject(
            [
                'change_frequency' => $data['changefreq'],
                'priority' => $data['priority'],
                'collection' => [$sitemap],
            ]
        );

        return $sitemapRow;
    }
}
