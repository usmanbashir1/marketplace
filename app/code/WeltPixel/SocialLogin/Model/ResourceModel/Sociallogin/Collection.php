<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 */

namespace WeltPixel\SocialLogin\Model\ResourceModel\Sociallogin;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package WeltPixel\SocialLogin\Model\ResourceModel\Sociallogin
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';


    protected function _construct()
    {
        $this->_init('WeltPixel\SocialLogin\Model\Sociallogin', 'WeltPixel\SocialLogin\Model\ResourceModel\Sociallogin');
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->join(
            ['cex' => $this->getTable('customer_entity')],
            'cex.entity_id = main_table.customer_id',
            ['firstname','lastname','email']
        );
    }

}