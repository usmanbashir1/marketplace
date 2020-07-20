<?php
namespace WeltPixel\ProductLabels\Controller\Adminhtml\ProductLabels;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use WeltPixel\ProductLabels\Model\Config\FileUploader\FileProcessor;


class SaveImage extends \WeltPixel\ProductLabels\Controller\Adminhtml\ProductLabels
{
    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileProcessor $fileProcessor
    ) {
        $this->fileProcessor = $fileProcessor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->fileProcessor->saveToTmp(key($_FILES));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
