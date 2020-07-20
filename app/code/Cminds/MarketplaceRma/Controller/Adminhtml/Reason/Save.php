<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Reason;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Model\Reason as RmaReason;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Reason
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_REASON_CREATE = 'marketplacerma/reason/create';

    const PATH_MARKETPLACERMA_REASON_INDEX = 'marketplacerma/reason/index';

    /**
     * @var RmaReason
     */
    private $rmaReason;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Save constructor.
     *
     * @param Context      $context
     * @param RmaReason    $rmaReason
     * @param DateTime     $dateTime
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        RmaReason $rmaReason,
        DateTime $dateTime,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->rmaReason = $rmaReason;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->_request->getParams()
            && $this->_request->getParam('name') !== ''
            && $this->_request->getParam('name') !== null
        ) {
            $data = $this->_request->getParams();
            $msgFlag = "";
            if ($data['id'] != '') {
				$msgFlag = "edit";
                $model = $this->rmaReason->load($data['id']);
            } else {
				$msgFlag = "add";
                $model = $this->rmaReason;
            }

            $data['created_at'] = $this->dateTime->timestamp();
            $model->setData('name', $data['name']);
            $model->setData('created_at', $data['created_at']);

            try {
                $model->save();
                if($msgFlag == "add"){
					$this->messageManager->addSuccessMessage(
						__('Reason was successfully added.')
					);
				} else {
					$this->messageManager->addSuccessMessage(
						__('Reason was successfully updated.')
					);
				}
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please provide reason name.'));

            return $this->_redirect(self::PATH_MARKETPLACERMA_REASON_CREATE);
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_REASON_INDEX);
    }
}
