<?php
namespace WeltPixel\Quickview\Controller\Adminhtml\Custommessages;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package WeltPixel\Quickview\Controller\Adminhtml\Custommessages
 */
class Save extends \WeltPixel\Quickview\Controller\Adminhtml\Custommessages
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;


    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
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

            /** @var \WeltPixel\Quickview\Model\QuickviewMessages $model */
            $model = $this->_objectManager->create('WeltPixel\Quickview\Model\QuickviewMessages')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This custom message no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            $model->setData($data);
            $model->loadPost($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the custom message.'));
                $this->dataPersistor->clear('weltpixel_quickviewmessage');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the custom message.') .  $e->getMessage());
            }

            $this->dataPersistor->set('weltpixel_quickviewmessage', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
