<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

use Magento\Customer\Model\EmailNotificationInterface;

/**
 * Class Sociallogin
 * @package WeltPixel\SocialLogin\Model
 */
class Sociallogin extends \Magento\Framework\Model\AbstractModel
{

    const PROVIDER_FIRSTNAME_PLACEHOLDER = 'FirstName';
    const PROVIDER_LASTNAME_PLACEHOLDER = 'LastName';

    /**
     * @var null | string
     */
    protected $_type = '';

    /**
     * @var string
     */
    protected $_accessToken = '';

    /**
     * @var bool
     */
    protected $_curlHeader = false;
    /**
     * @var EmailNotificationInterface
     */
    protected $emailNotificationInterface;

    /**
     * @var array
     */
    private $headerArray = [];

    /**
     * @var null | int
     */
    protected $_websiteId = '';

    /**
     * @var null | string
     */
    protected $_redirectUri = '';

    /**
     * @var array
     */
    protected $_userData = [];

    /**
     * @var null | string | int
     */
    protected $_applicationId = '';

    /**
     * @var null | string | int
     */
    protected $_secret = '';

    /**
     * @var string
     */
    protected $_rpCode = 'code';

    /**
     * @var null | array | string
     */
    protected $_callInfo = null;

    /**
     * @var array
     */
    protected $_gender = [
        'male',
        'female'
    ];

    /**
     * @var \WeltPixel\SocialLogin\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Model\Attribute
     */
    protected $attribute;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;


    /**
     * Sociallogin constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \WeltPixel\SocialLogin\Helper\Data $slHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Attribute $attribute
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerData
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param EmailNotificationInterface $emailNotificationInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\SocialLogin\Helper\Data $slHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Attribute $attribute,
        \Magento\Framework\Math\Random $random,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Model\EmailNotificationInterface $emailNotificationInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_helper = $slHelper;
        $this->storeManager = $storeManager;
        $this->store = $store;
        $this->encryptor = $encryptor;
        $this->customer = $customer;
        $this->eavConfig = $eavConfig;
        $this->attribute = $attribute;
        $this->random = $random;
        $this->customerSession = $customerSession;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->dir = $dir;
        $this->customerRepository = $customerRepository;
        $this->customerDataFactory = $customerData;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->emailNotificationInterface = $emailNotificationInterface;
    }

    public function _construct()
    {
        $this->_init('WeltPixel\SocialLogin\Model\ResourceModel\Sociallogin');
        $this->_websiteId = $this->storeManager->getWebsite()->getId();
        $this->_redirectUri = $this->_helper->getCallback($this->_type);
        $this->_applicationId = trim($this->_helper->getConfig($this->_helper->getConfigSectionId() . '/' . $this->_type . '/app_id'));
        $this->_secret = trim($this->_helper->getConfig($this->_helper->getConfigSectionId() . '/' . $this->_type . '/app_secret'));
    }

    /**
     * @param $customerId
     * @return $this
     * @throws \Exception
     */
    public function setCustomerByUser($customerId)
    {
        $data = [
            'type' => $this->_type,
            'sociallogin_id' => $this->fetchSocialUserData('user_id'),
            'customer_id' => $customerId
        ];

        $this->addData($data)->save();
        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getCustomerIdByUser()
    {
        $customerId = $this->_getCustomerIdByUser();
        if (!$customerId && $this->_helper->isGlobalScope()) {
            $customerId = $this->_getCustomerIdByUser(true);
        }

        return $customerId;
    }

    /**
     * @param bool $useGlobalScope
     * @return int|mixed
     */
    protected function _getCustomerIdByUser($globalScope = false)
    {
        $customerId = false;

        if ($this->fetchSocialUserData('user_id')) {
            $collection = $this->getCollection()
                ->join(['ce' => 'customer_entity'], 'ce.entity_id = main_table.customer_id', null)
                ->addFieldToFilter('main_table.type', $this->_type)
                ->addFieldToFilter('main_table.sociallogin_id', $this->fetchSocialUserData('user_id'))
                ->setPageSize(1);

            if ($globalScope === false) {
                $collection->addFieldToFilter('ce.website_id', $this->_websiteId);
            }

            $customerId = $collection->getFirstItem()->getData('customer_id');
        }

        return $customerId;
    }

    /**
     * @param $email
     * @param null $websiteId
     * @return \Magento\Customer\Model\Customer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerByEmail($email, $websiteId = null)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();

        $customer->setWebsiteId($websiteId ?: $this->storeManager->getWebsite()->getId());
        $customer->loadByEmail($email);

        return $customer;
    }

    /**
     * @return int
     */
    public function getCustomerIdByUserEmail()
    {
        $customerId = $this->_getCustomerIdByUserEmail();
        if (!$customerId && $this->_helper->isGlobalScope()) {
            $customerId = $this->_getCustomerIdByUserEmail(true);
        }
        return $customerId;
    }

    /**
     * @param bool $globalScope
     * @return int
     */
    protected function _getCustomerIdByUserEmail($globalScope = false)
    {
        $customerId = 0;
        if ($this->fetchSocialUserData('email')) {
            $collection = $this->customer->getCollection()
                ->addFieldToFilter('email', $this->fetchSocialUserData('email'))
                ->setPageSize(1);

            if ($globalScope === false) {
                $collection->addFieldToFilter('website_id', $this->_websiteId);
            }

            $customerId = $collection->getFirstItem()->getId();
        }

        return $customerId;
    }

    /**
     * @param $customerId
     * @param bool $globalScope
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getUsersByCustomerId($customerId, $globalScope = false)
    {
        $collection = $this->getCollection()
            ->join(['ce' => 'customer_entity'], 'ce.entity_id = main_table.customer_id', ['firstname', 'lastname', 'email'])
            ->addFieldToFilter('main_table.customer_id', $customerId);

        if ($globalScope === false) {
            $collection->addFieldToFilter('ce.website_id', $this->_websiteId);
        }

        return $collection;
    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function unlinkUser($id)
    {
        if (!$id) {
            return 'No link id provided';
        }
        $link = $this->load($id);
        $link->delete();

        return true;

    }

    /**
     * @return int|mixed
     */
    public function createNewCustomer()
    {
        $customerId = false;
        $errors = [];
        $customer = $this->customer->setId(null);

        try {
            $customer->setData($this->fetchSocialUserData())
                ->setConfirmation($this->fetchSocialUserData('password'))
                ->setPasswordConfirmation($this->fetchSocialUserData('password'))
                ->setData('is_active', 1)
                ->getGroupId();

            $errors = $this->_validate($customer);
            $correctEmail = \Zend_Validate::is($this->fetchSocialUserData('email'), 'EmailAddress');
            if ((empty($errors) || $this->_helper->validateIgnore()) && $correctEmail) {
                $customerId = $customer->save()->getId();
                $customer->setConfirmation(null)->save();
            }
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $this->setCustomer($customer);
        $this->setErrors($errors);

        return $customerId;
    }

    /**
     * @param $data
     * @param $store
     * @return mixed
     * @throws \Exception
     */
    public function createCustomerSocial($data, $store)
    {
        $errors = [];

        /** @var CustomerInterface $customer */
        $customer = $this->customerDataFactory->create();
        $customer->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setCreatedIn($store->getName());

        try {
            $customer = $this->customerRepository->save($customer);
            $newPasswordToken = $this->random->getUniqueHash();
            $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);

            $this->getEmailNotification()->newAccount($customer, EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD,'', $store->getId());

            $this->setUser($data['identifier'], $customer->getId(), $data['type']);
        } catch (AlreadyExistsException $e) {
            $errors[] = $e->getMessage();
            throw new InputMismatchException(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (\Exception $e) {
            if ($customer->getId()) {
                $this->_registry->register('isSecureArea', true, true);
                $this->customerRepository->deleteById($customer->getId());
            }
            $errors[] = $e->getMessage();
            throw $e;
        }

        /** @var Customer $customer */
        $customer = $this->customerFactory->create()->load($customer->getId());

        $this->setErrors($errors);

        return $customer;
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        return $this->emailNotificationInterface;
    }

    /**
     * @param $identifier
     * @param $customerId
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setUser($identifier, $customerId, $type)
    {
        $this->setData([
            'sociallogin_id' => $identifier,
            'customer_id' => $customerId,
            'type' => $type
        ])
            ->setId(null)
            ->save();

        return $this;
    }


    /**
     * @param $customer
     * @return array
     */
    protected function _validate($customer)
    {
        $errorArr = [];
        $valid = $customer->validate();
        if (true !== $valid) {
            $errors = $valid;
        }

        return $errorArr;
    }

    /**
     * @return string
     */
    public function getRpCode()
    {
        return $this->_rpCode;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function setUserData($key, $value = null)
    {
        if (is_array($key)) {
            $this->_userData = array_merge($this->_userData, $key);
        } else {
            $this->_userData[$key] = $value;
        }
        return $this;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function fetchSocialUserData($key = null)
    {
        if ($key !== null) {
            return isset($this->_userData[$key]) ? $this->_userData[$key] : null;
        }
        return $this->_userData;
    }

    /**
     * @param $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setSocialUserData($data)
    {
        $_data = [];
        foreach ($this->_fields as $customerField => $userField) {
            $_data[$customerField] = ($userField && isset($data[$userField])) ? $data[$userField] : null;
        }

        if (empty($_data['firstname'])) {
            $_data['firstname'] = $this->_setFirstName();
        }

        if (empty($_data['lastname'])) {
            $_data['lastname'] = $this->_setLastName();
        }

        if (!empty($_data['gender'])) {
            $genderData = $this->eavConfig->getAttribute('customer', 'gender');
            if ($genderData && $genderOptions = $genderData->getSource()->getAllOptions(false)) {
                switch ($_data['gender']) {
                    case $this->_gender[0]:
                        $_data['gender'] = $genderOptions[0]['value'];
                        break;
                    case $this->_gender[1]:
                        $_data['gender'] = $genderOptions[1]['value'];
                        break;
                    default:
                        $_data['gender'] = 0;
                }
            } else {
                $_data['gender'] = 0;
            }
        } else {
            $_data['gender'] = 0;
        }

        $_data['taxvat'] = '0';
        $_data['password'] = $this->_getRandomPassword();

        return $_data;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _setEmail($userData)
    {
        $email = $userData['id'] . '@' . $this->_type . '.com';
        return $email;
    }

    /**
     * @param $userData
     * @return mixed
     */
    protected function _setFirstName()
    {
        return self::PROVIDER_FIRSTNAME_PLACEHOLDER;
    }

    /**
     * @param $userData
     * @return mixed
     */
    protected function _setLastName()
    {
        return self::PROVIDER_LASTNAME_PLACEHOLDER;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getRandomPassword()
    {
        $len = 6;
        return $this->random->getRandomString($len);
    }


    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMail()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->customer->sendNewAccountEmail('registered', '', $storeId);

        return true;
    }

    /**
     * @return null|string
     */
    public function getProvider()
    {
        return $this->_type;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @param null $curlResource
     * @return mixed|null
     */
    protected function _apiCall($url, $paramsArr = [], $method = 'POST', $curlResource = null, $headerArr = [])
    {
        $result = null;
        $paramsStr = is_array($paramsArr) ? urlencode(http_build_query($paramsArr)) : urlencode($paramsArr);
        $curl = is_resource($curlResource) ? $curlResource : curl_init();
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode($paramsStr));
            if(!empty($headerArr)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
            }


        } else {
            if ($paramsStr) {
                $url .= '?' . urldecode($paramsStr);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /**
     * @param $token
     */
    protected function _setToken($token)
    {
        $this->_accessToken = $token;
    }

    /**
     * @param $url
     * @return mixed|string
     * @throws \Exception
     */
    public function httpGet($url)
    {
        $ch = $this->commonCurlParams($url);
        if ($this->_curlHeader) {
            $this->headerArray[] = 'Authorization: Bearer ' . $this->_accessToken;
        }

        $response = $this->execute($ch);

        return $response;
    }

    /**
     * @param $ch
     * @return mixed|string
     * @throws \Exception
     */
    protected function execute($ch)
    {
        $response = '';

        $this->headerArray[] = 'Expect:';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headerArray);
        $response = curl_exec($ch);
        if ($response === false) {
            $error_msg = "Unable to post request, underlying exception of " . curl_error($ch);
            curl_close($ch);
            throw new \Exception($error_msg);
        }

        curl_close($ch);

        return $response;
    }

    /**
     * @param $url
     * @return resource
     */
    protected function commonCurlParams($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    protected function _setCurlHeader()
    {
        $this->_curlHeader = true;
    }
}
