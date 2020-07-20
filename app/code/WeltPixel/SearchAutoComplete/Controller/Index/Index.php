<?php


namespace WeltPixel\SearchAutoComplete\Controller\Index;


use \Magento\Framework\App\Action\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\Controller\Result\RawFactory;
use \Magento\Framework\View\LayoutFactory;
use \Magento\Search\Model\QueryFactory;
use \Magento\Framework\Json\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var QueryFactory
     */
    protected $_queryFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultRawFactory
     * @param Data $jsonHelper
     * @param QueryFactory $queryFactory
     * @param LayoutFactory $layoutFactory
     * @param \WeltPixel\SearchAutoComplete\Helper\Data $configHelper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        Data $jsonHelper,
        QueryFactory $queryFactory,
        LayoutFactory $layoutFactory,
        \WeltPixel\SearchAutoComplete\Helper\Data $configHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_queryFactory = $queryFactory;
        $this->jsonHelper = $jsonHelper;
        $this->configHelper = $configHelper;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $responseData = [];
        $query = $this->_queryFactory->get();
        $query->setStoreId($this->_storeManager->getStore()->getId());
        $suggestionCollectionSize = $query->getSuggestCollection()->getSize();

        if ($query->getQueryText() != '') {
            if ($this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();
            }

            $this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class)->checkNotes();

            $blockSearch = $this->_view->getLayout()->createBlock('WeltPixel\SearchAutoComplete\Block\SearchAutoComplete');
            $layout = $this->layoutFactory->create();
            $productCollection = $blockSearch->getItemsCollection();
            $itemsCount = count($productCollection) - 1;
            if($itemsCount > 0) {
                $query->saveNumResults($itemsCount);
            }
            $categoryCollection = $blockSearch->getCategoryCollection();
            $categoryResultsBlock = $layout
                ->createBlock('WeltPixel\SearchAutoComplete\Block\SearchAutoComplete')
                ->setTemplate('WeltPixel_SearchAutoComplete::category_results.phtml')
                ->setData('categories', $categoryCollection)
                ->toHtml()
            ;

            if($this->configHelper->isEmptyCollection($productCollection)){
                $responseData['hasResults'] = 0;
                $responseData['results'] = '<div class="text-no-result">' . $this->configHelper->getTextForNoSearchResult() . '</div>';
                $responseData['categoryResults'] = $categoryResultsBlock;
                $responseData['suggestions'] = $suggestionCollectionSize;
                return $this->jsonResponse($responseData);
            }

            $resultsBlock = $layout
                ->createBlock('WeltPixel\SearchAutoComplete\Block\SearchAutoComplete')
                ->setTemplate('WeltPixel_SearchAutoComplete::results.phtml')
                ->setData('collection', $productCollection)
                ->toHtml()
            ;

            $responseData['hasResults'] = count($productCollection);
            $responseData['results'] = $resultsBlock;
            $responseData['categoryResults'] = $categoryResultsBlock;
            $responseData['suggestions'] = $suggestionCollectionSize;
        }

        try {
            return $this->jsonResponse($responseData);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
