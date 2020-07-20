<?php

namespace Cminds\SupplierInventoryUpdate\Block\Adminhtml\Edit\Tab\View;

use Cminds\Supplierfrontendproductuploader\Helper\Data as SupplierHelper;
use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;
use Cminds\SupplierInventoryUpdate\Model\Config\Source\Action as DropDownSource;
use Cminds\SupplierInventoryUpdate\Model\ResourceModel\InventoryUpdate\CollectionFactory as UpdateCollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterfaceFactory as CustomerRepository;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Registry;

class Form extends Extended
{
    private $coreRegistry;
    private $customerRepository;
    private $customerSession;
    private $updaterHelper;
    private $customerRepositoryInterface;
    private $updateCollectionFactory;
    private $urlInterface;
    private $supplierHelper;
    private $attributeFactory;
    private $dropDownSource;

    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        CustomerSession $customerSession,
        SupplierHelper $supplierHelper,
        UpdateCollectionFactory $updateCollectionFactory,
        UpdaterHelper $updaterHelper,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerRepository $customerRepository,
        Attribute $attributeFactory,
        DropDownSource $dropDownSource,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );

        $this->coreRegistry = $coreRegistry;
        $this->urlInterface = $context->getUrlBuilder();
        $this->updaterHelper = $updaterHelper;
        $this->customerSession = $customerSession;
        $this->supplierHelper = $supplierHelper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->updateCollectionFactory = $updateCollectionFactory;
        $this->customerRepository = $customerRepository->create();
        $this->attributeFactory = $attributeFactory;
        $this->dropDownSource = $dropDownSource;
    }

    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('comparedproduct_view_compared_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(5)->setCurPage(1);
    }

    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'catalog/product/edit',
            ['id' => $row->getProductId()]
        );
    }

    public function getSupplier()
    {
        $id = $this->getRequest()->getParam('id');
        $supplier = $this->customerRepository->getById($id);

        return $supplier;
    }

    public function getUpdateCollection($supplierId)
    {
        $collection = $this->updateCollectionFactory->create();
        foreach ($collection as $item) {
            if ($item->getSupplierId() == $supplierId) {
                return $item->getData();
            }
        }

        return $collection->getData();
    }

    public function getAllAvailableAttributes()
    {
        $attributeCollection = $this->attributeFactory->getCollection();
        $attributeCollection->addFieldToFilter('entity_type_id', 4);
        $values = [];

        foreach ($attributeCollection as $attribute) {
            $attributeLabel = $attribute->getStoreLabel();
            $attributeCode = $attribute->getAttributeCode();

            if ($attributeLabel !== null) {
                $values[] = [
                    'label' => $attributeLabel,
                    'code' => $attributeCode,
                ];
            }
        }

        return $values;
    }

    public function getActionOptions()
    {
        $allOptions = $this->dropDownSource->toOptionArray();

        return $allOptions;
    }
}
