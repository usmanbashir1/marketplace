<?php

namespace Cminds\SupplierSubscription\Controller\Plan;

use Cminds\SupplierSubscription\Controller\Plan;
use Magento\Framework\App\Action\Context;
use Cminds\Supplierfrontendproductuploader\Helper\Data;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Checkout\Model\Cart as CustomerCart;

class Purchase extends Plan
{
    /**
     * Validator object.
     *
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Form key.
     *
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var UrlEncoder
     */
    protected $urlEncoder;

    /**
     * @var CustomerCart
     */
    protected $cart;

    /**
     * Purchase constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param StoreManagerInterface $storeManage
     * @param ScopeConfigInterface $scopeConfig
     * @param Validator $formKeyValidator
     * @param FormKey $formKey
     * @param ProductRepository $productRepository
     * @param HelperContext $helperContext
     */
    public function __construct(
        Context $context,
        Data $helper,
        StoreManagerInterface $storeManage,
        ScopeConfigInterface $scopeConfig,
        Validator $formKeyValidator,
        FormKey $formKey,
        ProductRepository $productRepository,
        HelperContext $helperContext,
        CustomerCart $cart
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->formKey = $formKey;
        $this->productRepository = $productRepository;
        $this->urlEncoder = $helperContext->getUrlEncoder();
        $this->cart = $cart;

        parent::__construct(
            $context,
            $helper,
            $storeManage,
            $scopeConfig
        );
    }

    /**
     * Add subscription plan product to cart.
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->formKeyValidator->validate($this->getRequest())
            || $this->getRequest()->isPost() === false
        ) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

            return $resultRedirect;
        }

        $planProductId = $this->getRequest()->getParam('product_id');

        try {
            $planProduct = $this->productRepository->getById($planProductId);

            if (!$planProduct || !$planProduct->getId()) {
                $this->messageManager->addErrorMessage(__('Subscription product plan does not exists.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;
            }

            $qty = $this->getRequest()->getParam('qty');
            if (!is_numeric($qty) || (int)$qty < 1) {
                $qty = 1;
            }

            $continueUrl = $this->urlEncoder->encode('checkout/cart');
            $urlParamName = ActionInterface::PARAM_NAME_URL_ENCODED;

            $params = [
                'form_key' => $this->formKey->getFormKey(),
                'product' => $planProductId,
                'qty' => $qty,
                $urlParamName => $continueUrl
            ];

            $this->cart->addProduct($planProduct, $params);
            $this->cart->save();
            $resultRedirect->setUrl($this->buildUrl('checkout/cart/index'));
        } catch (InputException $e) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (AuthenticationException $e) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $this->messageManager->addExceptionMessage(
                $e,
                __('During adding subscription plan to cart error has occurred.')
            );
        }

        return $resultRedirect;
    }

    /**
     * Return generated url to provided route.
     *
     * @param string $route  Route string.
     * @param array  $params Params array.
     *
     * @return string
     */
    protected function buildUrl($route = '', $params = [])
    {
        /** @var \Magento\Framework\UrlInterface $urlBuilder */
        $urlBuilder = $this->_objectManager
            ->create('Magento\Framework\UrlInterface');

        return $urlBuilder->getUrl($route, $params);
    }
}
