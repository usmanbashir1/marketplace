<?php

namespace Cminds\MarketplaceRma\Controller\Adminhtml\Rma;

use Cminds\MarketplaceRma\Controller\Adminhtml\AbstractController;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Cminds\MarketplaceRma\Helper\Data;
use Cminds\MarketplaceRma\Model\ReturnProductFactory;
use Cminds\MarketplaceRma\Model\RmaFactory;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\NoteFactory;
use Cminds\MarketplaceRma\Model\CustomerAddressFactory;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\Manager;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\Adminhtml\Rma
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_RMA_INDEX = 'marketplacerma/rma/index';

    /**
     * @var Rma
     */
    private $rmaFactory;

    /**
     * @var Data
     */
    private $rmaHelper;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Manager
     */
    private $eventManager;

    /**
     * @var NoteFactory
     */
    private $noteFactory;

    /**
     * @var CustomerAddressFactory
     */
    private $customerAddressFactory;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ReturnProductFactory
     */
    private $returnProductFactory;

    /**
     * @var Context
     */
    private $context;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param Data                   $rmaHelper
     * @param RmaFactory             $rmaFactory
     * @param DateTime               $dateTime
     * @param ModuleConfig           $moduleConfig
     * @param Manager                $eventManager
     * @param NoteFactory            $noteFactory
     * @param CustomerAddressFactory $customerAddressFactory
     * @param RmaCollectionFactory   $rmaCollectionFactory
     * @param OrderFactory           $orderFactory
     * @param ReturnProductFactory   $returnProductFactory
     */
    public function __construct(
        Context $context,
        Data $rmaHelper,
        RmaFactory $rmaFactory,
        DateTime $dateTime,
        ModuleConfig $moduleConfig,
        Manager $eventManager,
        NoteFactory $noteFactory,
        CustomerAddressFactory $customerAddressFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        OrderFactory $orderFactory,
        ReturnProductFactory $returnProductFactory
    ) {
        parent::__construct($context, $moduleConfig);

        $this->rmaFactory = $rmaFactory;
        $this->rmaHelper = $rmaHelper;
        $this->dateTime = $dateTime;
        $this->eventManager = $eventManager;
        $this->noteFactory = $noteFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->returnProductFactory = $returnProductFactory;
    }

    /**
     * Execute method.
     *
     * @return ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        if ($this->_request->getParams()) {
            $data = $this->_request->getParams();

            if (isset($data['id']) && $data['id'] != '') {
                $model = $this->rmaFactory->create()->load($data['id']);
            } else {
                return $this->newRma();
            }

            if ($model->getData('status') == Rma::RMA_CLOSED) {
                $this->messageManager->addErrorMessage(__('You can not edit already closed Returns request.'));

                return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
            }

            $data['created_at'] = $this->dateTime->timestamp();
            $model->setData('status', $data['status']);
            $model->setData('package_opened', $data['package_opened']);
            $model->setData('request_type', $data['request_type']);
            $model->setData('additional_info', $data['additional_info']);
            $model->setData('reason', $data['reason']);

            // add comment to rma
            if (isset($data['rma_customer_notes_add'])
                && isset($data['rma_customer_notes_add']['content'])
            ) {
                if ($data['rma_customer_notes_add']['content'] != ''
                    && $data['rma_customer_notes_add']['content'] != null
                ) {
                    $content = $data['rma_customer_notes_add']['content'];

                    if (isset($data['rma_customer_notes_add']['notify']) && $data['rma_customer_notes_add']['notify'] == 'true') {
                        $this->eventManager->dispatch(
                            'cminds_marketplacerma_new_rma_note',
                            ['rma_model' => $model]
                        );
                        $notifyCustomer = 1;
                    } else {
                        $notifyCustomer = 0;
                    }
                    $noteFactory = $this->noteFactory->create();
                    $note = [
                        'rma_id' => $model->getData('id'),
                        'note' => $content,
                        'notify_customer' => $notifyCustomer
                    ];
                    $noteFactory->addData($note)->save();

                    $this->_eventManager->dispatch(
                        'cminds_marketplacerma_new_rma_note',
                        ['rma_model' => $model, 'notify_customer' => $notifyCustomer]
                    );
                }
            }

            // edit address
            if (isset($data['rma_customer_address'])) {
                $customerAddressData = $this->validateCustomerAddress($data['rma_customer_address']);

                if ($customerAddressData === false) {
                    $this->messageManager->addErrorMessage(__('Wrong data in Customer Address section.'));

                    return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
                }

                $customerAddress = $this->customerAddressFactory->create()->load($model->getData('id'));
                $customerAddress
                    ->addData(
                        [
                            'first_name' => $customerAddressData['first_name'],
                            'last_name' => $customerAddressData['last_name'],
                            'telephone' => $customerAddressData['telephone'],
                            'street' => $customerAddressData['return_address_street'],
                            'city' => $customerAddressData['return_address_city'],
                            'country' => $customerAddressData['return_address_country'],
                            'zipcode' => $customerAddressData['return_address_zipcode'],
                            'company'=>  $customerAddressData['company'],
                            'fax' => $customerAddressData['fax']
                        ]
                    )
                    ->save();
            }

            $currentStatus = $data['status'];
            $orderId = $model->getData('order_id');
            $isOrderInvoiced = $this->rmaHelper->checkIsOrderInvoiced($orderId);
            $isOrderReadyForCreditmemo = $this->rmaHelper->checkIsOrderReadyForCreditmemo($orderId);
            $isOrderHasCreditmemo = $this->rmaHelper->checkIsOrderHasCreditmemos($orderId);

            if ($isOrderInvoiced === false && $currentStatus == Rma::RMA_APPROVED) {
                $this->messageManager->addErrorMessage(__('To make Returns approved, order must be invoiced first'));

                return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
            }

            if ($isOrderInvoiced === true
                && $model->getOrigData('status') == Rma::RMA_APPROVED
                && $isOrderHasCreditmemo === true
            ) {
                $this->messageManager->addErrorMessage(
                    __('This Returns is already approved. You can only close this request.')
                );

                return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
            }

            if ($isOrderInvoiced === true
                && $data['status'] == Rma::RMA_APPROVED
                && $isOrderReadyForCreditmemo === true
                && $isOrderHasCreditmemo === false
            ) {
                $refund = $this->rmaHelper->proceedRefund($model->getData('order_id'));

                if ($refund === false) {
                    $this->messageManager->addErrorMessage(__('Something went wrong during refund proceed.'));

                    return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
                }
            }

            try {
                $model->save();

                if ($model->isObjectNew() === true) {
                    $this->eventManager->dispatch(
                        'cminds_marketplacerma_new_rma',
                        ['rma_model' => $model]
                    );

                    $this->messageManager->addSuccessMessage(
                        __('Returns was successfully added.')
                    );
                } else {
                    if ($model->getData('status') != $model->getOrigData('status')) {
                        if ($model->getData('status') == Rma::RMA_APPROVED) {
                            $this->eventManager->dispatch(
                                'cminds_marketplacerma_rma_approval',
                                ['rma_model' => $model]
                            );
                        } else {
                            $this->eventManager->dispatch(
                                'cminds_marketplacerma_status_changed',
                                ['rma_model' => $model]
                            );
                        }
                    }

                    $this->messageManager->addSuccessMessage(
                        __('Returns was successfully updated.')
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
    }

    /**
     * Validate Customer Adress params.
     *
     * @param $data
     *
     * @return bool
     */
    private function validateCustomerAddress($data)
    {
        if ($data['first_name'] == '' || $data['first_name'] == null) {
            return false;
        }

        if ($data['last_name'] == '' || $data['last_name'] == null) {
            return false;
        }

        if ($data['telephone'] == '' || $data['telephone'] == null) {
            return false;
        }

        if ($data['return_address_street'] == '' || $data['return_address_street'] == null) {
            return false;
        }

        if ($data['return_address_city'] == '' || $data['return_address_city'] == null) {
            return false;
        }

        if ($data['return_address_country'] == '' || $data['return_address_country'] == null) {
            return false;
        }

        if ($data['return_address_zipcode'] == '' || $data['return_address_zipcode'] == null) {
            return false;
        }

        return $data;
    }

    /**
     * @return ResponseInterface
     */
    private function newRma()
    {
        $params = $this->getRequest()->getParams();
        $paramsInSession = $this->_getSession()->getNewRmaTempData();
        $model = $this->rmaFactory->create();

        // Check is rma exists for this order.
        $rmaCollection = $this->rmaCollectionFactory
            ->create()
            ->addFieldToFilter('order_id', $paramsInSession['order_id']);
        if ($rmaCollection->count() > 0) {
            $this->messageManager->addErrorMessage(__('Returns for this order is already exists.'));

            return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
        }

        $model->setData('order_id', $paramsInSession['order_id']);
        $model->setData('customer_id', $paramsInSession['customer_id']);
        $model->setData('status', Rma::RMA_OPEN);
        $model->setData('package_opened', $params['package_opened']);
        $model->setData('request_type', $params['request_type']);
        $model->setData('additional_info', $params['additional_info']);
        $model->setData('reason', $params['reason']);
        $model->setData('created_at', $this->dateTime->timestamp());

        try {
            $model->save();

            // save customer address related to the rma
            $order = $this->orderFactory->create()->load($model->getData('order_id'));
            $shippingAddress = $order->getShippingAddress()->getData();
            $customerAddressModel = $this->customerAddressFactory
                ->create()
                ->addData(
                    [
                        'rma_id' => $model->getId(),
                        'first_name' => $shippingAddress['firstname'],
                        'last_name' => $shippingAddress['lastname'],
                        'telephone' => $shippingAddress['telephone'],
                        'street' => $shippingAddress['street'],
                        'city' => $shippingAddress['city'],
                        'country' => $shippingAddress['country_id'],
                        'zipcode' => $shippingAddress['postcode'],
                        'company'=>  $shippingAddress['company'],
                        'fax' => $shippingAddress['fax'],
                        'created_at' => $this->dateTime->timestamp(),
                    ]
                )
                ->save();

            // return products
            $returnProducts = $paramsInSession['rma_products'];
            $orderId = $paramsInSession['order_id'];
            $this->saveReturnProducts($orderId, $returnProducts);

            $this->eventManager->dispatch(
                'cminds_marketplacerma_new_rma',
                ['rma_model' => $model]
            );

            $this->messageManager->addSuccessMessage(
                __('Returns was successfully added.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
    }

    /**
     * Save return products.
     *
     * @param $orderId
     * @param $returnProducts
     *
     * @return ResponseInterface
     */
    private function saveReturnProducts($orderId, $returnProducts)
    {
        $returnProducts = $this->decodeReturnProducts($orderId, $returnProducts);

        foreach ($returnProducts as $returnProduct) {
            if ($returnProduct['return_qty'] != 0
                && $returnProduct['return_qty'] != null
                && $returnProduct['return_qty'] != ''
            ) {
                $returnProductToSave = $this->returnProductFactory->create();
                $returnProductToSave->addData($returnProduct);

                try {
                    $returnProductToSave->save();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went wrong.'));

                    return $this->_redirect(self::PATH_MARKETPLACERMA_RMA_INDEX);
                }
            }
        }
    }

    /**
     * Decode products array.
     *
     * @param $orderId
     * @param $returnProducts
     *
     * @return array
     */
    private function decodeReturnProducts($orderId, $returnProducts)
    {
        $returnProductsArray = [];

        foreach ($returnProducts as $key => $value) {
            // Delimiter "#" is also set in front end.
            $decodedData = explode('#', $key);

            $returnProductsArray[] = [
                'order_id' => $orderId,
                'invoice_id' => $decodedData[0],
                'product_id' => $decodedData[1],
                'return_qty' => $value,
                'created_at' => $this->dateTime->timestamp()
            ];
        }

        return $returnProductsArray;
    }
}
