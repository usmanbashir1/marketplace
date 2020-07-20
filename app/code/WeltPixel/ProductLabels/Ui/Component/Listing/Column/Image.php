<?php
namespace WeltPixel\ProductLabels\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use WeltPixel\ProductLabels\Model\Config\FileUploader\FileProcessor as LabelImageProcessor;
use \Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class Image
 */
class Image extends Column
{
    /**
     * @var LabelImageProcessor
     */
    protected $imageProcesor;

    /**
     * @var AssetRepository
     */
    protected $assetRepo;


    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param LabelImageProcessor $imageProcesor
     * @param AssetRepository $assetRepo
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        LabelImageProcessor $imageProcesor,
        AssetRepository $assetRepo,
        array $components = [],
        array $data = []
    ) {
        $this->imageProcesor = $imageProcesor;
        $this->assetRepo = $assetRepo;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $items
     * @return array
     */
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $configOptions = $this->getConfiguration();
            $fieldName = $this->getData('name');
            $altName = $configOptions['altField'];
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$fieldName]) {
                    $imageSrc = $this->imageProcesor->getFinalMediaUrl($item[$fieldName]);
                    $item[$fieldName . '_src'] = $imageSrc;
                    $item[$fieldName . '_orig_src'] = $imageSrc;
                    $item[$fieldName . '_alt'] = $altName;
                } else {
                    $noImageSrc = $this->assetRepo->getUrl('WeltPixel_ProductLabels::images/no_image_selected_placeholder.png');
                    $item[$fieldName . '_src'] = $noImageSrc;
                    $item[$fieldName . '_orig_src'] = $noImageSrc;
                    $item[$fieldName . '_alt'] = 'No image uploaded';
                }
            }
        }

        return $dataSource;
    }
}
