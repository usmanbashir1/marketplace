<?php
/**
 * Cminds SupplierRedirection
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
namespace Cminds\SupplierRedirection\Observer\Customer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Cminds SupplierRedirection supplier profile save controller.
 *
 * @category Cminds
 * @package  Cminds_SupplierRedirection
 */
class Save implements ObserverInterface
{
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
     * CustomerRepositoryInterface object.
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * Observer constructor.
     *
     * @param UrlRewrite $urlRewrite UrlRewrite Factory
     * @param StoreManagerInterface $storeManager StoreManager Interface
     * @param CustomerRepositoryInterface $customerRepositoryInterface CustomerRepositoryInterface
     *
     */
    public function __construct(
        UrlRewriteFactory $urlRewrite,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->urlRewrite = $urlRewrite;
        $this->storeManager = $storeManager;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(Observer $observer)
    {
        $urlId = 0;
        $requestPath = '';
        $customerDataObject = $observer->getEvent()->getCustomer();
        $customerModel      = $this->customerRepositoryInterface
            ->getById($customerDataObject->getId());
        $domainUrlObject    = $customerModel->getCustomAttribute('domain_url');
        $domainUrl          = '';
        if ($domainUrlObject != null) {
            $domainUrl = $domainUrlObject->getValue();
        }

        $targetPath         = 'marketplace/supplier/view/id/'. $customerModel->getId();
        $urlRewrite         = $this->urlRewrite->create();
        $urlCollection      = $urlRewrite->getCollection()
            ->addFieldToFilter('target_path', $targetPath);

        foreach ($urlCollection as $rewrite) {
            /** @var \Magento\UrlRewrite\Model\UrlRewrite $rewrite */
            $rewrite->delete();
        }

        if ($domainUrl != '') {
            $allStores = $this->storeManager->getStores();
            $idPath = rand(1, 99999);

            foreach ($allStores as $store) {
                $this->urlRewrite->create()
                    ->load($urlId)
                    ->setStoreId($store->getId())
                    ->setIsSystem(0)
                    ->setIdPath($idPath)
                    ->setTargetPath($targetPath)
                    ->setRequestPath($domainUrl)
                    ->save();
            }
        }

        return $this;
    }
}
