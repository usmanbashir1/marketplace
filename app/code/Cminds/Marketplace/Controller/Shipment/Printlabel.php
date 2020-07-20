<?php

namespace Cminds\Marketplace\Controller\Shipment;

use Cminds\Marketplace\Controller\AbstractController;
use Cminds\Marketplace\Model\Pdf;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Shipment\Track as SalesOrderTrack;
use Magento\Store\Model\StoreManagerInterface;

class Printlabel extends AbstractController
{
    protected $registry;
    protected $track;
    protected $pdf;
    protected $fileFactory;
    protected $dateTime;
    protected $shipment;

    public function __construct(
        Context $context,
        Data $helper,
        SalesOrderTrack $track,
        Pdf $pdf,
        FileFactory $fileFactory,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->track = $track;
        $this->pdf = $pdf;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
    }

    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }

        $id = $this->getRequest()->getParam('id');

        try {
            $track = $this->track->load($id);

            $model = $this->pdf;

            $shipments = [];
            $shipments[] = $track->getShipment();

            if ($track) {
                $model->setOrderId($track->getOrderId());
                $model->setCarrier($track->getCarrierCode());

                $pdf = $model->getPdf($shipments);

                return $this->fileFactory->create(
                    'label-' . $this->dateTime->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    DirectoryList::UPLOAD
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_redirect('*/order');
    }
}
