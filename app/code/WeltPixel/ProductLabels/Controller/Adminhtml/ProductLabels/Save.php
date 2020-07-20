<?php
namespace WeltPixel\ProductLabels\Controller\Adminhtml\ProductLabels;

use Magento\Backend\App\Action\Context;
use WeltPixel\ProductLabels\Model\ProductLabels;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use WeltPixel\ProductLabels\Model\Config\FileUploader\FileProcessor;

/**
 * Class Save
 * @package WeltPixel\ProductLabels\Controller\Adminhtml\ProductLabels
 */
class Save extends \WeltPixel\ProductLabels\Controller\Adminhtml\ProductLabels
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;


    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        FileProcessor $fileProcessor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fileProcessor = $fileProcessor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');

            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var \WeltPixel\ProductLabels\Model\ProductLabels $model */
            $model = $this->_objectManager->create('WeltPixel\ProductLabels\Model\ProductLabels')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This product label no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            /** Product & Category Image Save */
            $images = [
                'product' => 'product_image',
                'category' => 'category_image'
            ];

            foreach ($images as $entity => $imageField) {
                $entityImage = (isset($data[$imageField])) ? $data[$imageField][0] : null;
                if ($entityImage) {
                    /** Nothing was changed on the images */
                    if (isset($entityImage['existingImage'])) {
                        $data[$imageField] = $entityImage['existingImage'];
                    } else {
                        /** New Image was uploaded */
                        try {
                            $entityImagePath = $this->fileProcessor->saveToPath($entityImage, $entity);
                            $data[$imageField] = $entityImagePath;
                        } catch (\Exception $ex) {
                            $this->messageManager->addError($ex->getMessage());
                        }
                    }
                } else {
                    /** Image was deleted, or not uploaded at all */
                    $data[$imageField] = null;
                }
            }

//
//            $productImage = (isset($data['product_image'])) ? $data['product_image'][0] : null;
//
//            if ($productImage) {
//                if (isset($productImage['existingImage'])) {
//                    $data['product_image'] = $productImage['existingImage'];
//                } else {
//                    try {
//                        $productImagePath = $this->fileProcessor->saveToPath($productImage, 'product');
//                        $data['product_image'] = $productImagePath;
//                    } catch (\Exception $ex) {
//                        $this->messageManager->addError($ex->getMessage());
//                    }
//                }
//            } else {
//                $data['product_image'] = null;
//            }

            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            if(isset($data['product_page_position']) && $data['product_page_position'] != 1) {
                $data['product_position'] = 10;
            }

            $model->setData($data);
            $model->loadPost($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the product label.'));
                $this->dataPersistor->clear('weltpixel_productlabel');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the product label.') .  $e->getMessage());
            }

            $this->dataPersistor->set('weltpixel_productlabel', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
