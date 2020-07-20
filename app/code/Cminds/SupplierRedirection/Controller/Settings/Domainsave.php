<?php
/**
 * Cminds SupplierRedirection
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
namespace Cminds\SupplierRedirection\Controller\Settings;

use Cminds\SupplierRedirection\Controller\AbstractController;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\Interceptor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

/**
 * Cminds SupplierRedirection supplier profile save controller.
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
class Domainsave extends AbstractController
{

    /**
     * Interceptor object.
     *
     * @var Interceptor
     */
    protected $interceptor;

    /**
     * Session object.
     *
     * @var Session
     */
    protected $customerSession;

    /**
     * Customer object.
     *
     * @var Customer
     */
    protected $customerRepositoryInterface;

    /**
     * Customer factory object.
     *
     * @var Customer
     */
    protected $customerFactory;

    /**
     * URL Rewrite Factory object.
     *
     * @var UrlRewrite
     */
    protected $urlRewrite;

    /**
     * StoreManagerInterface object.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Object constructor.
     *
     * @param Context                     $context       Context object.
     * @param Data                        $helper        Data helper object.
     * @param Interceptor                 $interceptor   Interceptor object.
     * @param Session                     $session       Session object.
     * @param CustomerFactory             $customerFactory  CustomerFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface  CustomerRepositoryInterface
     * @param StoreManagerInterface       $storeManager  StoreManagerInterface
     * @param ScopeConfigInterface        $scopeConfig   ScopeConfigInterface
     * @param UrlRewriteFactory           $urlRewrite    UrlRewriteFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        Interceptor $interceptor,
        Session $session,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UrlRewriteFactory $urlRewrite
    ) {
        parent::__construct(
            $context,
            $helper,
            $storeManager,
            $scopeConfig
        );

        $this->interceptor = $interceptor;
        $this->customerSession = $session;
        $this->customerFactory = $customerFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->urlRewrite = $urlRewrite;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute main controller logic.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->canAccess()) {
            return $this->redirectToLogin();
        }
        $postData = $this->getRequest()->getParams();

        $value = $this->scopeConfig->getValue(
            'configuration_marketplace/configure/enable_supplier_pages'
        );
        if (!$value) {
            $this->interceptor->setHeader('HTTP/1.1', '404 Not Found');
            $this->interceptor->setHeader('Status', '404 File not found');
            $this->_forward('defaultNoRoute');
        }

        try {
            $customerModel = null;
            if ($this->customerSession->isLoggedIn()) {
                $customerData = $this->customerFactory->create()
                    ->load($this->customerSession->getId());
                $customerModel = $this->customerRepositoryInterface
                    ->getById($this->customerSession->getId());
            }

            if (!$customerModel) {
                throw new LocalizedException(__('Supplier does not exists'));
            }

            if (isset($postData['domain_url'])) {
                $targetPath = 'marketplace/supplier/view/id/' .
                    $customerModel->getId();
                $urlId = 0;
                $urlRewrite = $this->urlRewrite->create();
                $urlCollection = $urlRewrite->getCollection()
                    ->addFieldToFilter('target_path', $targetPath);

                foreach ($urlCollection as $rewrite) {
                    /** @var \Magento\UrlRewrite\Model\UrlRewrite $rewrite */
                    $rewrite->delete();
                }
                if ($postData['domain_url'] != '') {

                    $allStores = $this->storeManager->getStores();
                    $idPath = rand(1, 99999);

                    foreach ($allStores as $store) {
                        $this->urlRewrite->create()
                            ->load($urlId)
                            ->setStoreId($store->getId())
                            ->setIsSystem(0)
                            ->setIdPath($idPath)
                            ->setTargetPath($targetPath)
                            ->setRequestPath($postData['domain_url'])
                            ->save();
                    }

                }

                $customerModel->setCustomAttribute(
                    'domain_url', $postData['domain_url']
                );
                $this->customerRepositoryInterface->save($customerModel);

                $this->messageManager->addSuccess(
                    __('Supplier URL was changed')
                );
            }

            $this->_redirect('supplierredirection/settings/domain');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('supplierredirection/settings/domain');
        }
    }
}
