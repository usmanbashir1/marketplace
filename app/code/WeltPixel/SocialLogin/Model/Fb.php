<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model;

/**
 * Class Fb
 * @package WeltPixel\SocialLogin\Model
 */
class Fb extends \WeltPixel\SocialLogin\Model\Sociallogin
{
    /**
     * @var string
     */
    protected $_type = 'fb';

    /**
     * @var string
     */
    protected $_url = 'https://www.facebook.com/dialog/oauth';

    /**
     * @var string
     */
    protected $_apiTokenUrl = 'https://graph.facebook.com/oauth/access_token';

    /**
     * @var string
     */
    protected $_apiGraphUrl = 'https://graph.facebook.com/me';

    /**
     * @var array
     */
    protected $_fields = [
        'user_id' => 'id',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
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

        $data = [];

        $params = [
            'client_id' => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code' => $response,
            'redirect_uri' => $this->_redirectUri
        ];

        $apiToken = false;
        if ($response = $this->_apiCall($this->_apiTokenUrl, $params, 'GET')) {
            $apiToken = @json_decode($response, true);
            if (!$apiToken) {
                parse_str($response, $apiToken);
            }
        }

        if (isset($apiToken['access_token'])) {
            $params = [
                'access_token' => $apiToken['access_token'],
                'fields' => implode(',', $this->_fields)
            ];

            if ($response = $this->_apiCall($this->_apiGraphUrl, $params, 'GET')) {
                $data = json_decode($response, true);
            }
        }

        if (!$this->_userData = $this->_setSocialUserData($data)) {
            return false;
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
