<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Status;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Status;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\Rma;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Status
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_STATUS_CREATE = 'marketplacerma/status/create';

    const PATH_MARKETPLACERMA_STATUS_INDEX = 'marketplacerma/status/index';
    /**
     * @var Status
     */
    private $status;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Save constructor.
     *
     * @param Context      $context
     * @param Status       $status
     * @param DateTime     $dateTime
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        Status $status,
        DateTime $dateTime,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->status = $status;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->_request->getParams()
            && $this->_request->getParam('name') !== ''
            && $this->_request->getParam('name') !== null
        ) {
            $data = $this->_request->getParams();
			$msgFlag = "";

            if ($data['id'] == Rma::RMA_CLOSED || $data['id'] == Rma::RMA_OPEN) {
                $this->messageManager->addNoticeMessage(
                    __('You can not edit default status.')
                );

                return $this->_redirect(self::PATH_MARKETPLACERMA_STATUS_INDEX);
            }

            if ($data['id'] != '') {
				$msgFlag = "edit";
                $model = $this->status->load($data['id']);
            } else {
				$msgFlag = "add";
                $model = $this->status;
            }

            $data['created_at'] = $this->dateTime->timestamp();
            $model->setData('name', $data['name']);
            $model->setData('created_at', $data['created_at']);

            try {
                $model->save();
                if($msgFlag == "add"){
					$this->messageManager->addSuccessMessage(
						__('Status was successfully added.')
					);
				} else {
					$this->messageManager->addSuccessMessage(
						__('Status was successfully updated.')
					);
				}
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please provide status name.'));

            return $this->_redirect(self::PATH_MARKETPLACERMA_STATUS_CREATE);
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_STATUS_INDEX);
    }
}