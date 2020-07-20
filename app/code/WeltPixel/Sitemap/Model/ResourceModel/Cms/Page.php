<?php

namespace WeltPixel\Sitemap\Model\ResourceModel\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Cms\Api\GetUtilityPageIdentifiersInterface;

/**
 * Class Page
 * @package WeltPixel\Sitemap\Model\ResourceModel\Cms
 */
class Page extends \Magento\Sitemap\Model\ResourceModel\Cms\Page
{
    /**
     * @var GetUtilityPageIdentifiersInterface
     */
    private $getUtilityPageIdentifiers;

    /**
     * Retrieve cms page collection array
     *
     * @param int $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'updated_at' => 'update_time']
        )->join(
            ['store_table' => $this->getTable('cms_page_store')],
            "main_table.{$linkField} = store_table.$linkField",
            []
        )->where(
            'main_table.is_active = 1'
        )->where(
            'main_table.identifier NOT IN (?)',
            $this->getUtilityPageIdentifiers()->execute()
        )->where(
            'store_table.store_id IN(?)',
            [0, $storeId]
        )->where(
            'main_table.exclude_from_sitemap = 0'
        );

        $pages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private function getUtilityPageIdentifiers()
    {
        if (!$this->getUtilityPageIdentifiers) {
            $this->getUtilityPageIdentifiers = ObjectManager::getInstance()->get(GetUtilityPageIdentifiersInterface::class);
        }
        return $this->getUtilityPageIdentifiers;
    }

}
