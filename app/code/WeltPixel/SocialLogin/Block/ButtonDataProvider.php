<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Block;

/**
 * Class ButtonDataProvider
 * @package WeltPixel\SocialLogin\Block
 */
class ButtonDataProvider extends \WeltPixel\SocialLogin\Block\SocialLogin
{
    /**
     * @var bool
     */
    protected $_content = false;

    /**
     * @return array
     */
    public function getButtons() {
        $buttons = [];

        foreach ($this->_socialMedia as $mediaType => $endPoint) {
            if($this->slHelper->getSocialConfig($mediaType, $mediaType.'_enabled')){
                $appId = $this->slHelper->getSocialConfig($mediaType, 'app_id');
                $url = $endPoint . $this->_getEndpointParams($mediaType, $appId);
                $buttons[$mediaType]['api_id'] = $appId;
                $buttons[$mediaType]['endpoint'] = $url;
            }
        }

        return $buttons;
    }

    /**
     * @param $type
     * @param $clientId
     */
    protected function _getEndpointParams($type, $clientId) {
        $params = '';
        if($type == 'fb') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&display=popup&redirect_uri='.$redirectUrl.'&scope=email';
        } elseif($type == 'amazon') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&redirect_uri='.$redirectUrl.'&response_type=code&scope=profile';
        } elseif($type == 'google') {
            $redirectUrl = $this->getUrl('sociallogin/account/login', ['type' => $type]);
            $params = 'client_id='.$clientId.'&redirect_uri='.$redirectUrl.'&response_type=code';
        } else {
            $params = '';
        }

        return $params;

    }

    /**
     * @param bool $flag
     */
    public function getContent($flag = true)
    {
        $this->_content = $flag;
    }

    /**
     * @return string
     */
    public function getEmailFormUrl()
    {
        return $this->getUrl('sociallogin/account/email', ['_secure' => true]);
    }

    public function _afterToHtml($html)
    {
        if ($this->_content && trim($html)) {
            $html = '<script type="text/javascript">'
                . 'window.' . $this->_content . ' = \'' . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs($html)) . '\';'
                . '</script>';
        } elseif($this->_content == 'socialloginButtons' && !$html) {
            $html = '<script type="text/javascript">'
                . 'window.' . $this->_content . ' = \'' . str_replace(["\n", 'script'], ['', "scri'+'pt"], $this->escapeJs('')) . '\';'
                . '</script>';
        }

        return parent::_afterToHtml($html);
    }

    /**
     * @return mixed
     */
    public function isSecure()
    {
        return (bool) $this->slHelper->isSecure();
    }

}