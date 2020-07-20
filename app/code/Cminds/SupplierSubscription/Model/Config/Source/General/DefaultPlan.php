<?php

namespace Cminds\SupplierSubscription\Model\Config\Source\General;

use Cminds\SupplierSubscription\Model\Plan as PlanModel;

class DefaultPlan extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Cminds\SupplierSubscription\Model\ResourceModel\Plan\Collection
     */
    protected $plans;

    /**
     * DefaultPlan constructor.
     *
     * @param PlanModel $plan
     */
    public function __construct(
        PlanModel $plan
    ) {
        $this->plans = $plan->getCollection();
    }

    /**
     * Retrieve all options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $options = [];
            foreach ($this->plans as $plan) {
                $options[] = ['value' => $plan->getId(), 'label' => $plan->getName()];
            }

            array_unshift($options, ['label' => 'None', 'value' => null]);
            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * Prepare and return array of option values.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
