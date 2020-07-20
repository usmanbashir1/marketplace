<?php

namespace Cminds\MarketplacePaypal\Model;

use Cminds\MarketplacePaypal\Helper\Rest as Helper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Token Provider
 *
 * @category Cminds
 * @package  Cminds_MarketplacePaypal
 * @author   Cminds Core Team <info@cminds.com>
 */
class TokenProvider
{
    /** @var Helper $helper */
    private $helper;
    /** @var JsonHelper $json */
    private $json;

    /**
     * TokenProvider constructor.
     * @param Helper $helper
     * @param JsonHelper $json
     */
    public function __construct(
        Helper $helper,
        JsonHelper $json
    ) {
        $this->helper = $helper;
        $this->json = $json;
    }

    /**
     * Get token
     *
     * @return string|null
     * @throws \Zend_Http_Client_Exception
     */
    function getToken()
    {
        $client = new \Zend_Http_Client($this->helper->getTokenUrl());
        $client->setAuth(
            $this->helper->getClientId(),
            $this->helper->getSecret()
        );

        $client->setParameterPost('grant_type', 'client_credentials');

        $response = $client->request('POST');
        $responseBody = $this->json->jsonDecode($response->getBody());

        return $responseBody['access_token'] ?? null;
    }
}
