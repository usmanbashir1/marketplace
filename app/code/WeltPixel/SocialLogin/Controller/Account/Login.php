<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Controller\Account;

/**
 * Class Login
 * @package WeltPixel\SocialLogin\Controller\Account
 */
class Login extends \WeltPixel\SocialLogin\Controller\AbstractAccount
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;
    /**
     * @var
     */
    protected $model;

    protected $formKey;

    /**
     * Login constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \WeltPixel\SocialLogin\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\View\Layout\Interceptor $layout
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \WeltPixel\SocialLogin\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Layout\Interceptor $layout,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Data\Form\FormKey $formKey
    )
    {
        parent::__construct($context, $customerSession, $dataHelper, $storeManager,$resultRawFactory, $layout);
        $this->customer = $customer;
        $this->_customerRepository = $customerRepository;
        $this->formKey = $formKey;
    }

    public function execute()
    {
        $session = $this->_getSession();
        $type = $this->getRequest()->getParam('type');
        $formKey = $this->formKey->getFormKey();

        if ($session->isLoggedIn()) {
            return $this->_windowClose();
        }

        if (!$type) {
            return $this->_windowClose();
        }

        $this->_setType($type);

        $this->model = $this->_getModel($this->type);

        $rpCodes = $this->model->getRpCode();

        if (is_array($rpCodes)) {
            $response = [];
            foreach ($rpCodes as $code) {
                $response[$code] = $this->getRequest()->getParam($code);
            }
        } else {
            $response = $this->getRequest()->getParam($rpCodes);
        }

        if (!$this->model->fetchUserData($response)) {
            return $this->_windowClose();
        }

        $newUserData = $this->model->fetchSocialUserData();
        $customerId = $this->model->getCustomerIdByUser();

        if ($customerId) {
            $redirectUrl = $this->_slHelper()->getRedirectUrl();
        } elseif ($customerId = $this->model->getCustomerIdByUserEmail()) {
            $this->model->setCustomerByUser($customerId);
            $message = __('Customer with email %1 already exists in the database. Your %2 Profile is linked to this customer.', '<b>'.$this->model->fetchSocialUserData('email'). '</b>', '<b>'.ucfirst($this->type).'</b>');
            $this->messageManager->addNotice($message);
            $redirectUrl = $this->_slHelper()->getRedirectUrl();
        } else {
            // instagram does not return the social user email address
            if(empty($this->model->fetchSocialUserData('email'))) {
                $this->customerSession->setUserProfile($newUserData);
                return $this->_appendJs("<script>window.close();window.opener.emailCallback();</script>");
            }
            $customer = $this->createCustomerProcess($newUserData);
            if ($customer) {
                $customerId = $customer->getId();
                $this->messageManager->addSuccess(__('Customer registration successful. Your password reset link was sent to the email: %1', $this->model->fetchSocialUserData('email')));
                $this->_dispatchRegisterSuccess($customer);
                $redirectUrl = $this->_slHelper()->getRedirectUrl();
            } else {
                $session->setCustomerFormData($newUserData);
                $redirectUrl = $this->_getUrl('customer/account/create', ['_secure' => true]);

                if ($errors = $this->model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addError($error);
                    }
                }
                $session->setData('sociallogin', [
                    'provider' => $this->model->getProvider(),
                    'user_id' => $this->model->fetchSocialUserData('user_id')
                ]);
            }
        }

        if ($customerId) {
            $customer = $this->customer->create()->load($customerId);
            $this->refresh($customer);

            $session->setData('sociallogin', [
                'provider' => $this->model->getProvider(),
                'sociallogin_id' => $this->model->fetchSocialUserData('user_id'),
                'sl_form_key' => $formKey
            ]);
            $this->_slHelper()->getReferer(null);
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode([
                'redirectUrl' => $redirectUrl
            ]));
        } else {
            $action = '
                var slDoc = window.opener ? window.opener.document : document;
                slDoc.getElementById("sociallogin-referer").value = "' . htmlspecialchars(base64_encode($redirectUrl)) . '";
                slDoc.getElementById("sociallogin-submit").click();
            ';

            $body = $this->attacheJs('if(window.opener && window.opener.location &&  !window.opener.closed) { window.close(); }; ' . $action . ';');
            $this->getResponse()->setBody($body);
        }
    }


}
