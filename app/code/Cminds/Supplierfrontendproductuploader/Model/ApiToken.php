<?php

namespace Cminds\Supplierfrontendproductuploader\Model;

use Magento\Framework\Registry ;
use Magento\Framework\Model\Context;
use Cminds\Supplierfrontendproductuploader\Api\Data\TokenInterface;
use Cminds\Supplierfrontendproductuploader\Model\ResourceModel\ApiToken\CollectionFactory as ApiTokenCollectionFactory;


class ApiToken extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Token collection factory.
     *
     * @var ApiTokenCollectionFactory
     */
    protected $apiTokenCollectionFactory;

    /**
     * Object constructor.
     *
     * @param Context                       $context
     * @param Registry                      $registry
     * @param ApiTokenCollectionFactory     $apiTokenCollectionFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ApiTokenCollectionFactory $apiTokenCollectionFactory
    ) {
        $this->apiTokenCollectionFactory = $apiTokenCollectionFactory;
        parent::__construct($context, $registry);
    }

    protected function _construct()
    {
        $this->_init(
            'Cminds\Supplierfrontendproductuploader\Model\ResourceModel\ApiToken'
        );
    }

    /**
     * Retrieve list of Attribute Sets
     *
     * @param string $token
     * @return int|bool
     */
    public function getCustomerIdByToken($token){
        $token = trim($token);
        $tokenCollection = $this->apiTokenCollectionFactory->create();
        $tokenCollection->addFieldToFilter(TokenInterface::TOKEN, $token);
        if(0 === $tokenCollection->count()){
            $token = false;
        } else {
            $token = $tokenCollection->getFirstItem()->getCustomerId();
        }
        return $token;
    }
}
