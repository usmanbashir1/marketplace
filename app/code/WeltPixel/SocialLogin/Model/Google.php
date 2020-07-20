<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

use WeltPixel\SocialLogin\lib\Google\Client as Google_Client;

/**
 * Class Google
 * @package WeltPixel\SocialLogin\Model
 */
class Google extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'google';
    /**
     * @var array
     */
    private $scopes = [
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/userinfo.email'
    ];
    /**
     * @var string
     */
    protected $_requestEndpoint = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=';
    /**
     * @var Google_Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $_fields = [
        'user_id' => 'id',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'email' => 'email',
        'gender' => 'gender'
    ];

    public function __construct(
        Google_Client $client,
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
        parent::__construct($context, $registry, $slHelper, $storeManager, $store, $encryptor, $customer, $eavConfig, $attribute, $random, $filesystem, $ioFile, $dir, $customerSession, $customerRepository, $customerData, $customerFactory, $accountManagement, $emailNotificationInterface, $resource, $resourceCollection, $data);
        $this->client = $client;
    }


    /**
     * @param $response
     * @return bool
     */
    public function fetchUserData($response)
    {
        if (empty($response)) {
            return false;
        }

        $data = $userData = [];

        $client = $this->client;
        $client->setClientId($this->_applicationId);
        $client->setClientSecret($this->_secret);
        $client->setRedirectUri($this->_redirectUri);
        $client->setScopes($this->scopes);
        $client->authenticate($_GET['code']);
        $token = $client->getAccessToken();
        if($token) {
            $tokenInfo = json_decode($token, true);
            $userDetails = file_get_contents($this->_requestEndpoint . $tokenInfo['access_token']);
            $data = json_decode($userDetails, true);
            if(!empty($data)) {
                $userData = [
                    'id' => $data['id'],
                    'email' => isset($data['email']) ? $data['email'] : '',
                    'firstname' => isset($data['given_name']) ? $data['given_name'] : self::PROVIDER_FIRSTNAME_PLACEHOLDER,
                    'lastname' => isset($data['family_name']) ? $data['family_name'] : self::PROVIDER_LASTNAME_PLACEHOLDER,
                    'gender' => isset($data['gender']) ? $data['gender'] : ''

                ];
            }

            if (!$this->_userData = $this->_setSocialUserData($userData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _setSocialUserData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        return parent::_setSocialUserData($data);
    }

}
