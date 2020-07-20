<?php
namespace WeltPixel\ProductLabels\Model\ProductLabels;

use WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use WeltPixel\ProductLabels\Model\Config\FileUploader\FileProcessor;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \WeltPixel\ProductLabels\Model\ResourceModel\ProductLabels\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $productLabelsCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param FileProcessor $fileProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $productLabelsCollectionFactory,
        DataPersistorInterface $dataPersistor,
        FileProcessor $fileProcessor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $productLabelsCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->fileProcessor = $fileProcessor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \WeltPixel\ProductLabels\Model\ProductLabels $item */
        foreach ($items as $item) {
            $itemData = $item->getData();

            if (isset($itemData['product_image'])) {
                $itemProductImage = [];
                $itemProductImage[0] = $this->fileProcessor->getImageDetails($itemData['product_image']);
                $itemData['product_image'] = $itemProductImage;
            }
            if (isset($itemData['category_image'])) {
                $itemCategoryImage = [];
                $itemCategoryImage[0] = $this->fileProcessor->getImageDetails($itemData['category_image']);
                $itemData['category_image'] = $itemCategoryImage;
            }
            $this->loadedData[$item->getId()] = $itemData;
        }

        $data = $this->dataPersistor->get('weltpixel_productlabel');
        if (!empty($data)) {
            $item = $this->collection->getNewEmptyItem();
            if (isset($data['product_image'])) {
                $itemProductImage = [];
                $itemProductImage[0] = $this->fileProcessor->getImageDetails($data['product_image']);
                $data['product_image'] = $itemProductImage;
            }
            if (isset($data['category_image'])) {
                $itemCategoryImage = [];
                $itemCategoryImage[0] = $this->fileProcessor->getImageDetails($data['category_image']);
                $data['category_image'] = $itemCategoryImage;
            }
            $item->setData($data);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('weltpixel_productlabel');
        }

        return $this->loadedData;
    }
}
