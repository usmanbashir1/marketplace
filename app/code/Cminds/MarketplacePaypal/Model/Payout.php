<?php

namespace Cminds\MarketplacePaypal\Model;

use Cminds\MarketplacePaypal\Model\PaymentStatus;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Cminds\MarketplacePaypal\Helper\Rest as Helper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Monolog\Logger;

/**
 * Payout Model
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class Payout implements PaymentInterface
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    /**
     * @var JsonHelper
     */
    private $json;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PaymentStatusFactory
     */
    private $paymentStatusFactory;

    /**
     * @var PaymentStatusRepository
     */
    private $paymentStatusRepository;

    /**
     * Payout constructor.
     * @param CustomerFactory $customerFactory
     * @param Helper $helper
     * @param TokenProvider $tokenProvider
     * @param JsonHelper $json
     * @param Logger $logger
     * @param PaymentStatusFactory $paymentStatusFactory
     * @param PaymentStatusRepository $paymentStatusRepository
     */
    public function __construct(
        CustomerFactory $customerFactory,
        Helper $helper,
        TokenProvider $tokenProvider,
        JsonHelper $json,
        Logger $logger,
        PaymentStatusFactory $paymentStatusFactory,
        PaymentStatusRepository $paymentStatusRepository
    ) {
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
        $this->tokenProvider = $tokenProvider;
        $this->json = $json;
        $this->logger = $logger;
        $this->paymentStatusFactory = $paymentStatusFactory;
        $this->paymentStatusRepository = $paymentStatusRepository;
    }

    /**
     * Pay
     *
     * @param int $supplierId
     * @param float $amount
     * @param int $orderId
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function pay(int $supplierId, float $amount, int $orderId)
    {
        if (!$token = $this->tokenProvider->getToken()) {
            throw new LocalizedException(__('Issue while token processing. Please, check your credentials'));
        }

        $supplier = $this->customerFactory->create()
            ->load($supplierId);
        $url = $this->helper->getPayoutUrl();
        $response = $this->request($url, $token, 'POST', $this->preparePayBody($supplier, $amount));

        $decodedResponse = $this->json->jsonDecode($response->getBody());
        $batchId = $decodedResponse['batch_header']['payout_batch_id'] ?? null;
        if (!$batchId) {
            throw new LocalizedException(__('Some troubles while parsing response. No batch id specified'));
        }
        try {
            $this->startStatusTracking($supplier, $amount, $orderId, $batchId);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Some troubles while saving payment status.'));
        }

        return;
    }

    /**
     * Prepare pay body
     *
     * @param Customer $supplier
     * @param float $amount
     * @return string
     * @throws LocalizedException
     */
    protected function preparePayBody(Customer $supplier, float $amount): string
    {
        if (!$supplier->getSupplierPaypalEmail()) {
            throw new LocalizedException(__('Supplier did not provide a paypal email'));
        }

        $uniqueBatchId = $this->generateBatchId();
        $uniqueItemId = $this->generateItemId($uniqueBatchId);

        return $this->json->jsonEncode(
            [
                'sender_batch_header' => [
                    'sender_batch_id' => $uniqueBatchId,
                    'email_subject' => $this->helper->getEmailSubject()
                ],
                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'receiver' => $supplier->getSupplierPaypalEmail(),
                        'sender_item_id' => $uniqueItemId,
                        'amount' => [
                            'currency' => $this->helper->getCurrency(),
                            'value' => $amount
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Generate batch id
     *
     * @return string
     */
    protected function generateBatchId(): string
    {
        return uniqid('', true);
    }

    /**
     * Generate item id
     *
     * @param string $batchId
     * @return string
     */
    protected function generateItemId(string $batchId): string
    {
        return $batchId . '_item';
    }

    /**
     * Start status tracking
     *
     * @param Customer $supplier
     * @param float $amount
     * @param int $orderId
     * @param string $batchId
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function startStatusTracking(
        Customer $supplier, float $amount, int $orderId, string $batchId
    ) {
        $paymentStatus = $this->paymentStatusFactory->create();
        $paymentStatus->setRecipientEmail($supplier->getSupplierPaypalEmail());
        $paymentStatus->setAmount($amount);
        $paymentStatus->setOrderId($orderId);
        $paymentStatus->setStatus(PaymentStatus::PENDING);
        $paymentStatus->setPayoutBatchId($batchId);
        $paymentStatus->setPaymentDate($this->helper->getCurrentDatetime());
        $paymentStatus->setSupplierId($supplier->getId());
        $this->paymentStatusRepository->save($paymentStatus);
    }

    /**
     * Update status
     *
     * @param \Cminds\MarketplacePaypal\Model\PaymentStatus $payoutStatus
     * @return \Cminds\MarketplacePaypal\Model\PaymentStatus
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function updateStatus(PaymentStatus $payoutStatus)
    {
        if (!$token = $this->tokenProvider->getToken()) {
            throw new LocalizedException(__('Issue while token processing. Please, check your credentials'));
        }

        $url = $this->helper->getPayoutUpdateUrl($payoutStatus->getPayoutBatchId());

        $response = $this->request($url, $token, 'GET');
        $decodedResponse = $this->json->jsonDecode($response->getBody());

        $status = $decodedResponse['batch_header']['batch_status'] ?? null;

        if (!$status) {
            throw new \Exception(__('No status specified in api response'));
        }

        $payoutStatus->setStatus($status);

        return $payoutStatus;
    }

    /**
     * Request
     *
     * @param string $url
     * @param string $token
     * @param string $method
     * @param string $body
     * @return \Zend_Http_Response
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    protected function request(
        string $url, string $token, string $method, string $body = '')
    {
        $client = new \Zend_Http_Client($url);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        if ($body) {
            $client->setRawData($body, 'application/json');
        }

        $response = $client->request($method);

        if (!$response->isSuccessful()) {
            $this->logger->info($client->getLastRequest());
            $this->logger->info($client->getLastResponse());
            throw new LocalizedException(__('Some issues while payment. Check payout log.'));
        }

        return $response;
    }
}
