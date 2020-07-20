<?php

namespace Cminds\MarketplaceRma\Controller\Rma;

use Cminds\Supplierfrontendproductuploader\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Cminds\MarketplaceRma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Cminds\MarketplaceRma\Model\Rma;
use Cminds\MarketplaceRma\Model\ReturnProductFactory;
use Cminds\MarketplaceRma\Model\CustomerAddressFactory;
use Cminds\MarketplaceRma\Model\Config as ModuleConfig;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Save
 *
 * @package Cminds\MarketplaceRma\Controller\Rma
 */
class Save extends AbstractController
{
    const PATH_MARKETPLACERMA_RMA_INDEX = 'marketplacerma/rma/index';
    const PATH_MARKETPLACERMA_RMA_CREATE = 'marketplacerma/rma/create';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Rma
     */
    private $rma;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RmaCollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ReturnProductFactory
     */
    private $returnProductFactory;

    /**
     * @var CustomerAddressFactory
     */
    private $customerAddressFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param Data                   $helper
     * @param Rma                    $rma
     * @param RmaCollectionFactory   $rmaCollectionFactory
     * @param CustomerSession        $customerSession
     * @param StoreManagerInterface  $storeManager
     * @param ScopeConfigInterface   $scopeConfig
     * @param DateTime               $dateTime
     * @param ModuleConfig           $moduleConfig
     * @param ReturnProductFactory   $returnProductFactory
     * @param CustomerAddressFactory $customerAddressFactory
     * @param OrderFactory           $orderFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        Rma $rma,
        RmaCollectionFactory $rmaCollectionFactory,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        DateTime $dateTime,
        ModuleConfig $moduleConfig,
        ReturnProductFactory $returnProductFactory,
        CustomerAddressFactory $customerAddressFactory,
        OrderFactory $orderFactory
    ) {
        $this->rma = $rma;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->moduleConfig = $moduleConfig;
        $this->returnProductFactory = $returnProductFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->orderFactory = $orderFactory;

        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );
    }

    /**
     * Execute method.
     *
     * @return bool|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->customerSession->authenticate();
        }

        if ($this->moduleConfig->isActive() === false) {
            $this->messageManager->addErrorMessage(__('MarketplaceRma is currently disabled in configuration'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $data = $this->getRequest()->getParams();
        $isDataValid = $this->validate($data);

        if ($isDataValid !== true) {
            return $isDataValid;
        }

        // check is Returns already exists for this order.
        $rmaCollection = $this->rmaCollectionFactory->create();
        $rmaCollection
            ->addFieldToFilter('order_id', $data['order_id'])
            ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId());

        if ($rmaCollection->count() > 0) {
            $this->messageManager->addErrorMessage(__('Returns for this order is already exists.'));
            $url = $this->storeManager->getStore()->getUrl(self::PATH_MARKETPLACERMA_RMA_INDEX);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($url);

            return $resultRedirect;
        }

        $model = $this->rma;

        $model->setData('order_id', $data['order_id']);
        $model->setData('package_opened', $data['package_opened']);
        $model->setData('additional_info', $data['additional_info']);
        $model->setData('request_type', $data['request_type']);
        $model->setData('reason', $data['reason']);
        $model->setData('customer_id', $this->customerSession->getCustomerId());
        $model->setData('status', Rma::RMA_OPEN);
        $model->setData('created_at', $this->dateTime->timestamp());

        // return products
        $returnProducts = $data['rma_products'];
        $orderId = $data['order_id'];
        $this->saveReturnProducts($orderId, $returnProducts);

        try {
            $model->save();
            $this->saveRmaCustomerAddress($model);
            $saveMessage = __('Your Returns has been submitted.');
            $this->messageManager->addSuccessMessage($saveMessage);

            $this->_eventManager->dispatch('cminds_marketplacerma_new_rma', ['rma_model' => $model]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong, please try again later.'));
        }

        $url = $this->storeManager->getStore()->getUrl(self::PATH_MARKETPLACERMA_RMA_INDEX);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }

    /**
     * Validate customer given data before save.
     *
     * @param $data
     *
     * @return bool|ResultInterface
     */
    private function validate($data)
    {
        $orderIdIsValid = true;
        $packageOpenedIsValid = true;
        $additionalInfoIsValid = true;
        $requestTypeIsValid = true;
        $reasonIsValid = true;
        $rmaProductsIsValid = true;

        if (isset($data['order_id']) === false
            || ($data['order_id'] === null || $data['order_id'] === '')
        ) {
            $orderIdIsValid = false;
            $this->messageManager->addErrorMessage(__('Please select order ID.'));
        }

        if (isset($data['package_opened']) === false
            || ($data['package_opened'] === null || $data['package_opened'] === '')
        ) {
            $packageOpenedIsValid= false;
            $this->messageManager->addErrorMessage(__('Please choose is package opened or not.'));
        }

        if (isset($data['additional_info']) === false
            || ($data['additional_info'] === null || $data['additional_info'] === '')
        ) {
            $additionalInfoIsValid= false;
            $this->messageManager->addErrorMessage(__('Please type additional information.'));
        }

        if (isset($data['request_type']) === false
            || ($data['request_type'] === null || $data['request_type'] === '')
        ) {
            $additionalInfoIsValid= false;
            $this->messageManager->addErrorMessage(__('Please select request type.'));
        }

        if (isset($data['reason']) === false
            || ($data['reason'] === null || $data['reason'] === '')
        ) {
            $requestTypeIsValid= false;
            $this->messageManager->addErrorMessage(__('Please select reason.'));
        }

        if (isset($data['rma_products']) === false
            || ($data['rma_products'] === null || $data['rma_products'] === '')
        ) {
            $rmaProductsIsValid = false;
            $this->messageManager->addErrorMessage(__('Please select products qty.'));
        } elseif (isset($data['rma_products']) === true) {
            $atLeastOneSelected = 0;

            foreach ($data['rma_products'] as $key => $value) {
                $atLeastOneSelected += $value;
            }

            if ($atLeastOneSelected == 0) {
                $this->messageManager->addErrorMessage(__('Please select products qty.'));
                $rmaProductsIsValid = false;
            }
        }

        if ($orderIdIsValid === false
            || $packageOpenedIsValid === false
            || $additionalInfoIsValid === false
            || $requestTypeIsValid === false
            || $reasonIsValid === false
            || $rmaProductsIsValid === false
        ) {
            $url = $this->storeManager->getStore()->getUrl(self::PATH_MARKETPLACERMA_RMA_INDEX);
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($url);

            return $resultRedirect;
        }

        return true;
    }

    /**
     * Save return products.
     * You can find all rma related products by order id.
     *
     * @param $orderId
     * @param $returnProducts
     *
     * @return ResultInterface
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
                    $url = $this->storeManager->getStore()->getUrl(self::PATH_MARKETPLACERMA_RMA_INDEX);
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setUrl($url);

                    return $resultRedirect;
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
            // delimiter "#" is also set in front end
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

    /**
     * Save rma related address.
     *
     * @param $model
     * @throws \Exception
     */
    private function saveRmaCustomerAddress($model)
    {
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
                    'fax' => $shippingAddress['fax']
                ]
            )
            ->save();
    }
}
