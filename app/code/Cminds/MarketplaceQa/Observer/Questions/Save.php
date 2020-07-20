<?php

namespace Cminds\MarketplaceQa\Observer\Questions;

use Cminds\MarketplaceQa\Helper\Data as MarketplaceQaHelper;
use Cminds\MarketplaceQa\Helper\EmailSender;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface
{
    const QA_MODEL = 'qa_model';

    /**
     * @var MarketplaceQaHelper
     */
    private $marketplaceQaHelper;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * Save constructor.
     *
     * @param MarketplaceQaHelper $marketplaceQaHelper
     * @param EmailSender $emailSender
     */
    public function __construct(
        MarketplaceQaHelper $marketplaceQaHelper,
        EmailSender $emailSender
    ) {
        $this->marketplaceQaHelper = $marketplaceQaHelper;
        $this->emailSender = $emailSender;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->marketplaceQaHelper->marketplaceQaEnabled() === false) {
            return;
        }

        $model = $observer->getData(self::QA_MODEL);
        $question = $model->getData('question');

        if ($this->marketplaceQaHelper->notifySupplierAboutNewQuestion() === true) {
            $this->emailSender->prepareEmail($question, $model, true);
        }

        if ($this->marketplaceQaHelper->notifyAdminAboutNewQuestion() === true) {
            $this->emailSender->prepareEmail($question, $model, false, true);
        }
    }
}
