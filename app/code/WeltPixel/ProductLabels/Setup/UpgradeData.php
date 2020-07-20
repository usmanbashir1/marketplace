<?php

namespace WeltPixel\ProductLabels\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use WeltPixel\ProductLabels\Model\ProductLabelsFactory;
use WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels\CollectionFactory;
use Magento\Framework\App\State;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ProductLabelsFactory
     */
    private $productLabelsFactory;

    private $collectionFactory;

    /**
     * @var State
     */
    private $appState;

    /**
     * Init
     *
     * @param ProductLabelsFactory $productLabelsFactory
     * @param State $appState
     */
    public function __construct(ProductLabelsFactory $productLabelsFactory, CollectionFactory $collectionFactory, State $appState)
    {
        $this->productLabelsFactory = $productLabelsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->appState = $appState;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        try {
            if(!$this->appState->isAreaCodeEmulated()) {
                $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
            }
        } catch (\Exception $ex) {}

        if (version_compare($context->getVersion(), '1.0.4') < 0) {

            $collection = $this->collectionFactory->create();

            if($collection->getSize() < 1) {
                $sampleData = [
                    [
                        'title' => 'NEW',
                        'priority' => '0',
                        'status' => '0',
                        'store_id' => '0',
                        'customer_group' => '0,1,2,3',
                        'product_position' => '3',
                        'product_text' => 'NEW',
                        'product_text_bg_color' => '#000000',
                        'product_text_font_size' => '12px',
                        'product_text_font_color' => '#FFFFFF',
                        'product_text_padding' => '8px 15px',
                        'product_css' => 'border-radius:30px; margin-top: 12px;',
                        'category_position' => '3',
                        'category_text' => 'NEW',
                        'category_text_bg_color' => '#000000',
                        'category_text_font_size' => '11px',
                        'category_text_font_color' => '#FFFFFF',
                        'category_text_padding' => '8px 15px 8px 15px',
                        'category_css' => 'border-radius:30px; ',
                    ],
                    [
                        'title' => 'Sale',
                        'priority' => '0',
                        'status' => '0',
                        'store_id' => '0',
                        'customer_group' => '0,1,2,3',
                        'product_position' => '1',
                        'product_text' => 'SALE',
                        'product_text_bg_color' => '#D83701',
                        'product_text_font_size' => '11px',
                        'product_text_font_color' => '#FFFFFF',
                        'product_text_padding' => '8px 15px 8px 15px',
                        'product_css' => '',
                        'category_position' => '1',
                        'category_text' => 'SALE',
                        'category_text_bg_color' => '#D83701',
                        'category_text_font_size' => '11px',
                        'category_text_font_color' => '#FFFFFF',
                        'category_text_padding' => '8px 15px 8px 15px',
                        'category_css' => '',
                    ],
                ];

                foreach ($sampleData as $data) {
                    $productLabel = $this->productLabelsFactory->create();
                    $productLabel->setData($data);
                    try {
                        $productLabel->save();
                    } catch (\Exception $ex) {}
                }
            }

        }

        $setup->endSetup();
    }
}