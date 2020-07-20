<?php

namespace WeltPixel\CategoryPage\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $_conditionsHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $_categoryRepository;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * Category constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Widget\Helper\Conditions $conditionHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Widget\Helper\Conditions $conditionHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Helper\Category $categoryHelper
    ) {
        parent::__construct($context);
        $this->_conditionsHelper = $conditionHelper;
        $this->_categoryRepository = $categoryRepository;
        $this->_categoryHelper = $categoryHelper;
    }

    /**
     * Returns from a widget condition the name and url for the used category in the condition
     * @param $condition
     * @return array
     */
    public function getCategoryDetailsFromCondition($condition) {
        $result = [
            'name' => '',
            'url' => ''
        ];
        $conditionValue = $this->_conditionsHelper->decode($condition);
        if (is_array($conditionValue)) {
            foreach ($conditionValue as $condition) {
                if (isset($condition['attribute']) && ($condition['attribute'] == 'category_ids') && (isset($condition['value']))) {
                    try {
                        $category = $this->_categoryRepository->get($condition['value']);
                        $result['name'] = $category->getName();
                        $result['url'] = $this->_categoryHelper->getCategoryUrl($category);
                    } catch (\Exception $ex) {}
                }
            }
        }

        return $result;
    }
}
