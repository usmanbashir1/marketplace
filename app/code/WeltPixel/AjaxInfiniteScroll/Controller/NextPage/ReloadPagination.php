<?php


namespace WeltPixel\AjaxInfiniteScroll\Controller\NextPage;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;


class ReloadPagination extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Data
     */
    protected $_jsonHelper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;


    /**
     * ReloadPagination constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $jsonHelper
     * @param LoggerInterface $logger
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        LoggerInterface $logger,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager


    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->resultFactory = $context->getResultFactory();
        $this->_logger = $logger;
        $this->_categoryRepository = $categoryRepository;
        $this->_storeManager = $storeManager;
        parent::__construct($context);


    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $isAjax = false;
        $responseData = [];
        $params = $this->getRequest()->getParams();

        if (isset($params['is_ajax']) && $params['is_ajax']) {
            $isAjax = true;
        }

        if ($isAjax) {
            $filterParams = $this->_getUrlFilterParams($params['pager_url']);
            $category = (isset($params['category_id']) && !empty($params['category_id'])) ? $this->_categoryRepository->get($params['category_id'], $this->_storeManager->getStore()->getId()) : false;

            if ($category) {
                $layout = $this->_resultPageFactory->create()->getLayout();
                $productList = $layout->createBlock('Magento\Catalog\Block\Product\ListProduct');

                $collection = $productList->setCategoryId($category->getId())
                    ->injectAttributeFilters($filterParams)
                    ->getLoadedProductCollection();
                $collection = $collection->setCurPage($params['p']);

                $pagerBlock = $layout
                    ->createBlock('Magento\Catalog\Block\Product\Widget\Html\Pager')
                    ->setTemplate('Magento_Theme::html/pager.phtml')
                    ->setUseContainer(false)
                    ->setCollection($collection);

                $toolbarBlock = $layout
                    ->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar')
                    ->setTemplate('Magento_Catalog::product/list/toolbar.phtml')
                    ->setCollection($collection);

                $responseData = [
                    'errors' => false,
                    'pager' => $pagerBlock->toHtml(),
                    'toolbar' => $toolbarBlock->toHtml()
                ];
            } else {
                $responseData = [
                    'errors' => true
                ];
            }

        }

        try {
            return $this->jsonResponse($responseData);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }


    /**
     * @param $urlParam
     * @return array
     */
    protected function _getUrlFilterParams($urlParam) {
        $url = explode("?", $urlParam);
        $urlParamArr = (isset($url[1])) ? explode("&", $url[1]) : false;
        $params = [];
        if(!$urlParamArr) {
            return $params;
        }
        foreach($urlParamArr as $urlParam) {
            $paramArr = explode('=', $urlParam);
            $paramStrClean = urldecode($paramArr[1]);
            if($paramArr[0] != 'q')
                $params[$paramArr[0]] = explode(',', $paramStrClean);
        }

        return $params;
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->_jsonHelper->jsonEncode($response)
        );
    }
}
