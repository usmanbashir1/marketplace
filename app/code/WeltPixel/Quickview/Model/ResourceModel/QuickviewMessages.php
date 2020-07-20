<?php
namespace WeltPixel\Quickview\Model\ResourceModel;

/**
 * Class QuickviewMessages
 * @package WeltPixel\Quickview\Model\ResourceModel
 */
class QuickviewMessages extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_quickviewmessages', 'id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Exception
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = $object->getData('store_id');
        if (is_array($storeId)) {
            $object->setData('store_id', implode(",", $storeId));
        }

        $customerGroup = $object->getData('customer_group');
        if (is_array($customerGroup)) {
            $object->setData('customer_group', implode(",", $customerGroup));
        }


        $validFrom = $object->getData('valid_from');
        $validTo = $object->getData('valid_to');
        if ($validTo && $validFrom) {

            $minValue = strtotime($validFrom);
            $maxValue = strtotime($validTo);

            if ($minValue > $maxValue) {
                $message = __('Make sure the To Date is later than or the same as the From Date.');
                $eavExc = new \Exception($message);
                throw $eavExc;
            }
        }

        return $this;
    }
}
