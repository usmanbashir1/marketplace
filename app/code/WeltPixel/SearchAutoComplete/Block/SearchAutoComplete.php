<?php

namespace WeltPixel\SearchAutoComplete\Block;

use \Magento\Backend\Block\Template\Context;
use WeltPixel\SearchAutoComplete\Model\Autocomplete\SearchDataProvider;


class SearchAutoComplete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var SearchDataProvider
     */
    protected $_dataProvider;

    /**
     * SearchAutoComplete constructor.
     * @param Context $context
     * @param SearchDataProvider $dataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        SearchDataProvider $dataProvider,
        array $data = []
    )
    {
        $this->_dataProvider = $dataProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return array|\Magento\Search\Model\Autocomplete\ItemInterface[]
     */
    public function getItemsCollection()
    {
        return $this->_dataProvider->getItems();
    }

    public function getCategoryCollection()
    {
        return $this->_dataProvider->getCategoryItems();
    }

}
