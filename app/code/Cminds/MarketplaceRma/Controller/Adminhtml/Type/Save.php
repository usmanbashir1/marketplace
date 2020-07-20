<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Type;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Type;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Type
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_TYPE_CREATE = 'marketplacerma/type/create';

    const PATH_MARKETPLACERMA_TYPE_INDEX = 'marketplacerma/type/index';

    /**
     * @var Type
     */
    private $rmaType;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Save constructor.
     *
     * @param Context      $context
     * @param Type         $type
     * @param DateTime     $dateTime
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        Type $type,
        DateTime $dateTime,
        ModuleConfig $moduleConfig
    ) {
        parent::__construct($context, $moduleConfig);

        $this->rmaType = $type;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute method.
     *
     * @return \Magento\Framework\App\ResponseInterface|ResultInterface
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
                $model = $this->rmaType->load($data['id']);
            } else {
				$msgFlag = "add";
                $model = $this->rmaType;
            }

            $data['created_at'] = $this->dateTime->gmtTimestamp();
            $model->setData('name', $data['name']);
            $model->setData('created_at', $data['created_at']);

            try {
                $model->save();
                if($msgFlag == "add"){
					$this->messageManager->addSuccessMessage(__('Type was successfully added.'));
				} else {
					$this->messageManager->addSuccessMessage(__('Type was successfully updated.'));
				}
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please provide type name.'));

            return $this->_redirect(self::PATH_MARKETPLACERMA_TYPE_CREATE);
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_TYPE_INDEX);
    }
}
