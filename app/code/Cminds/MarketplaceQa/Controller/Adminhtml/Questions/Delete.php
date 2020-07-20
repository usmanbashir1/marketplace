<?php

namespace Cminds\MarketplaceQa\Controller\Adminhtml\Questions;

use Cminds\MarketplaceQa\Model\Qa;
use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * @var Qa
     */
    protected $qa;

    public function __construct(Action\Context $context, Qa $qa)
    {
        parent::__construct($context);
        $this->qa = $qa;
    }

    public function execute()
    {
        $qaId = $this->getRequest()->getParam('id', false);
        if ($qaId) {
            $qa = $this->qa;
            $qa->load($qaId);

            if (!$qa->getId()) {
                $this->messageManager->addError(
                    __('This field no longer exists.')
                );
            }

            try {
                $qa->delete();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(
                    __('Can not delete this question.')
                );
            }
        }

        return $this->_redirect(
            '*/*/index'
        );
    }
}
