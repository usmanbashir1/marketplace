<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Sociallogin
 * @package WeltPixel\SocialLogin\Model\ResourceModel
 */
class Sociallogin extends AbstractDb
{
    /**
     * AttributeOptions constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('weltpixel_sociallogin_customer', 'id');
    }
}