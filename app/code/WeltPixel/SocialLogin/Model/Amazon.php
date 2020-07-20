<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Amazon
 * @package WeltPixel\SocialLogin\Model
 */
class Amazon extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'amazon';
    /**
     * @var string
     */
    protected $_apiBaseUrl = 'https://api.amazon.com';
    /**
     * @var string
     */
    protected $_apiAuthorizeUrl = 'https://www.amazon.com/ap/oa';
    /**
     * @var string
     */
    protected $_apiTokenUrl = 'https://api.amazon.com/auth/o2/token';
    /**
     * @var string
     */
    protected $_apiTokenRequestUrl = 'https://api.amazon.com//auth/o2/tokeninfo?access_token=';


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

    public function _construct()
    {
        parent::_construct();
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

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'grant_type' =>  'authorization_code',
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'POST')) {
            $apiToken = @json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
            $data = json_decode($response, true);
            if(isset($data['access_token'])){
                $token = $data['access_token'];
                $this->_setToken($token);

                $reqUrl = $this->_apiTokenRequestUrl . $this->urlEncode($token);
                $apiDetails = $this->httpGet($reqUrl);
                $data = json_decode($apiDetails);

                if ($data->aud != $this->_applicationId) {
                    throw new \Exception('The Access Token belongs to neither your Client ID nor App ID');
                }

                $userData = $this->_getUserInfo();
            }
        }

        if (!$this->_userData = $this->_setSocialUserData($userData)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getUserInfo() {

        $userData = [];
        $this->_setCurlHeader();
        $url = $this->_apiBaseUrl . '/user/profile';

        $response = $this->httpGet($url);

        $respObj = json_decode($response);

        $userData = $this->_setUserName($respObj->name);
        $userData['id']= $respObj->user_id;
        $userData['email']= $respObj->email ? : '';
        $userData['gender']= '';

        return $userData;
    }

    /**
     * @param $userData
     */
    protected function _setUserName($name) {
        if($name) {
            $nameArr = explode(' ', $name);
            $userData['firstname'] = (isset($nameArr[0])) ? $nameArr[0] : self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = (isset($nameArr[1])) ? $nameArr[1] : self::PROVIDER_LASTNAME_PLACEHOLDER;
        } else {
            $userData['firstname'] = self::PROVIDER_FIRSTNAME_PLACEHOLDER;
            $userData['lastname'] = self::PROVIDER_LASTNAME_PLACEHOLDER;
        }

        return $userData;
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

    private function urlEncode($value)
    {
        return str_replace('%7E', '~', rawurlencode($value));
    }

}
