<?php

namespace WeltPixel\SampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class Page
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->pageFactory = $pageFactory;
    }

    /**
     * @param array $fixtures
     * @param mixed $sliderId
     * @param mixed $blockId
     * @throws \Exception
     */
    public function install(array $fixtures, $sliderId = [], $blockId = [])
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

                switch ($row['identifier']) {
                    case 'home-page-v1':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom"  type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v5':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom"  type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}', '{{widget_condition_2}}'];
                        $conditionWidgetsContent = [
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]',
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`15`^]^]'
                        ];
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);

                        break;
                    case 'home-page-v7':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom"  type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v8':
                        $widgetPlaceholders = ['{{widget_nr_1}}', '{{widget_nr_2}}'];
                        $widgetContents = [
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}',
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[1] .'"}}'
                        ];
                        $row['content'] = str_replace($widgetPlaceholders,$widgetContents, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}'];
                        $conditionWidgetsContent = [
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`2`^]^]'
                        ];
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);

                        break;
                    case 'home-page-v9':
                        $widgetPlaceholders = ['{{widget_nr_1}}', '{{widget_nr_2}}'];
                        $widgetContents = [
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}',
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[1] .'"}}'
                        ];
                        $row['content'] = str_replace($widgetPlaceholders,$widgetContents, $row['content']);
                        break;
                    case 'home-page-v10':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}'];
                        $conditionWidgetsContent = [
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]'
                        ];
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);
                        break;
                    case 'home-page-v12':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v15':
                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}', '{{widget_condition_2}}', '{{widget_condition_3}}'];
                        $conditionWidgetsContent = [
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]',
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`25`^]^]',
                            '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`15`^]^]'
                        ];
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);
                        break;
                    case 'weltpixel-brands-widget' :
                        $widgetPlaceholders = ['{{widget_nr_1}}', '{{widget_nr_2}}', '{{widget_nr_3}}', '{{widget_nr_4}}', '{{widget_nr_5}}', '{{widget_nr_6}}'];
                        $widgetContents = [
                            '{{widget type="WeltPixel\DesignElements\Block\Widget\Brand" type_name="WeltPixel Brands" style="grid-6" block_id="'. $blockId[0] .'"}}',
                            '{{widget type="WeltPixel\DesignElements\Block\Widget\Brand" type_name="WeltPixel Brands" style="grid-4" block_id="'. $blockId[0] .'"}}',
                            '{{widget type="WeltPixel\DesignElements\Block\Widget\Brand" type_name="WeltPixel Brands" style="grid-3" block_id="'. $blockId[1] .'"}}',
                            '{{widget type="WeltPixel\DesignElements\Block\Widget\Brand" type_name="WeltPixel Brands" style="grid-2" block_id="'. $blockId[2] .'"}}',
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" type_name="WeltPixel - Custom Slider Widget" slider_id="'. $sliderId[0] .'"}}',
                            '{{widget type="WeltPixel\DesignElements\Block\Widget\Brand" type_name="WeltPixel Brands" style="grid-4" block_id="'. $blockId[0] .'"}}'
                        ];
                        $row['content'] = str_replace($widgetPlaceholders,$widgetContents, $row['content']);
                        break;
                }

                $this->pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->save();
            }
        }
    }
}
