<?php

namespace Cminds\Marketplace\Controller\Checkout;

use Cminds\Marketplace\Helper\Supplier;
use Cminds\Marketplace\Model\Methods as MethodsModel;
use Cminds\Marketplace\Model\RatesFactory;
use Cminds\Marketplace\Model\Shipping\Carrier\Marketplace\Shipping;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\CustomerFactory as CustomerFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\Marketplace\Helper\Data;
use Cminds\Supplierfrontendproductuploader\Helper\Price;

class GetProductsByVendors extends Action
{
    protected $productFactory;
    protected $customerFactory;
    protected $store;
    protected $methods;
    protected $carrierModel;
    protected $supplierHelper;
    protected $session;
    protected $jsonResultFactory;
    protected $marketplaceHelper;
    protected $priceHelper;
    private $ratesFactory;
    private $json;
    private $regionFactory;
    private $countryFactory;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        CustomerFactory $customerFactory,
        StoreManagerInterface $store,
        MethodsModel $methodsModel,
        Shipping $carrierModel,
        Supplier $supplierHelper,
        Session $session,
        JsonFactory $jsonFactory,
        Data $marketplaceHelper,
        Price $price,
        RatesFactory $ratesFactory,
        Json $json,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory
    ) {
        parent::__construct($context);

        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->store = $store;
        $this->methods = $methodsModel;
        $this->carrierModel = $carrierModel;
        $this->supplierHelper = $supplierHelper;
        $this->session = $session;
        $this->jsonResultFactory = $jsonFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->priceHelper = $price;
        $this->ratesFactory = $ratesFactory;
        $this->json = $json;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $json = $this->getRequest()->getParams();
        $items = json_decode($json['json'], true);
        $shippingAddress = $this->json->unserialize($json['shippingAddress'] ?? []);
        $productsBySuppliers = [];

        foreach ($items as $item) {
            $product = $this->productFactory->create()
                ->load($item['product']['entity_id']);
            $product->setQty($item['qty'] ?? 1);
            $product->setCartPrice($item['price_incl_tax']);
            $productData = $product->getData();
            if (isset($productData['thumbnail'])) {
                $productData['productImage'] = $this->store->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . 'catalog/product' . $productData['thumbnail'];
            } else {
                $productData['productImage'] = $this->store->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_STATIC)
                    . 'frontend/Magento/luma/en_US/Magento_Catalog/'
                    . 'images/product/placeholder/image.jpg';
            }

            if ($product->getCreatorId() === null) {
                $productsBySuppliers['non_supplier'][] = $productData;
            } else {
                $productsBySuppliers[$product->getCreatorId()][] = $productData;
            }
        }

        $output = [];

        foreach ($productsBySuppliers as $supplierId => $products) {
            $methods = $this->supplierHelper->getShippingMethods(
                $supplierId,
                $products
            );

            $methodsArr = [];
            $selected = $this->session->getMarketplaceShippingMethods();

            foreach ($methods as $method) {
                $methodData = $method->getData();
                if (isset($selected[$supplierId])
                    && $selected[$supplierId]['method_id'] === $methodData['id']
                ) {
                    $methodData['checked'] = true;
                } else {
                    $methodData['checked'] = false;
                }

                $totalItems = [];
                foreach ($products as $product) {
                    if ($product['type_id'] != 'configurable') {
                        $qty = $product['qty'];

                        if ($creatorId = $product['creator_id']) {
                            $totalItems[$creatorId][] = [
                                $product['weight'],
                                $product['cart_price'],
                                $qty,
                            ];
                        }
                    }
                }

                if ($methodData['table_rate_available']) {
                    $rateModel = $this->ratesFactory->create();
                    $rateModel->load($methodData['id'], 'method_id');

                    $price = $rateModel->getId() ? $this->calculateTableRate(
                        $totalItems, $shippingAddress, $rateModel, $methodData['table_rate_condition']
                    ) : null;

                    if (is_null($price)) {
                        continue;
                    }

                    $methodData['price'] = $price;
                }

                if (isset($methodData['price'])) {
                    $convertedPrice = $this->priceHelper->convertToCurrentCurrencyPrice((double)$methodData['price']);
                    $currencySymbol = $this->priceHelper->getCurrentCurrencySymbol();
                    $methodData['converted_price'] = $currencySymbol . $convertedPrice;
                }

                $methodsArr[] = $methodData;
            }

            if ($supplierId === 'non_supplier') {
                $selected['non_supplier'] = [
                    'method_id' => null,
                    'price' => $this->carrierModel
                        ->getSupplierShippingPriceNonSupplier(),
                ];

                $price_total = $this->supplierHelper
                    ->calculateTotalShippingPrice($selected);
                $this->session
                    ->setMarketplaceShippingMethods($selected);
                $this->session
                    ->setMarketplaceShippingPrice($price_total);
            }

            $supplierName = $this->supplierHelper->getSupplierNameForShippingMethods($supplierId);
            if ($supplierId != 'non_supplier' && !empty($methodsArr)) {
                    $output[] = [
                        'supplier_id' => $supplierId,
                        'supplier_name' => $supplierName,
                        'products' => $products,
                        'methods' => $methodsArr,
                    ];
            } else {
                $output = [];
            }
        }

        return $result->setData(
            $output
        );
    }


    public function calculateTableRate($items, $address, $rateModel, $type)
    {
        return $this->calculateTableFee($items, $address, $rateModel, $type);
    }

    protected function calculateTableFee($items, $address, $model, $type)
    {
        $country = $address['countryId'] ?? null;
        $regionFromRequest = $address['regionId'] ?? $address['region'] ?? null;
        $postcode = $address['postcode'] ?? null;

        if (!$country) {
            return null;
        }

        $regionModel = $this->regionFactory->create();
        if (is_numeric($regionFromRequest)) {
            $regionModel->load($regionFromRequest);
        } else {
            $regionModel->loadByName($regionFromRequest, $country);
        }

        $countryModel = $this->countryFactory->create();
        $countryModel->loadByCode($country);
        if (!$countryModel->getId()) {
            return null;
        }
        $country = $countryModel->getData('iso3_code');
        $region = $regionModel->getCode();

        $total = 0;

        foreach ($items as $item) {
            $item = reset($item);
            if (isset($item[$type - 1])) {
                if ($type == 3) {
                    $total += $item[$type - 1];
                } else {
                    $total += ($item[$type - 1] * $item[2]);
                }
            }
        }

        if ($type == 1) {
            return $model->getRateByWeight(
                $country,
                $region,
                $postcode,
                $total
            );
        } else {
            if ($type == 2) {
                return $model->getRateByPrice(
                    $country,
                    $region,
                    $postcode,
                    $total
                );
            } else {
                if ($type == 3) {
                    return $model->getRateByQty(
                        $country,
                        $region,
                        $postcode,
                        $total
                    );
                }
            }
        }

        return 0;
    }

}
