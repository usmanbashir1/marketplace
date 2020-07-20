<?php
namespace WeltPixel\ProductPage\Controller\Adminhtml\Version;
use WeltPixel\ProductPage\Model\ProductPageFactory;

/**
 * Class Version
 * @package WeltPixel\ProductPage\Controller\Adminhtml\Version
 */
class Version extends \Magento\Backend\App\Action {

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @var \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @var ProductPageFactory
	 */
	protected $productPageFactory;

	/**
	 * Version constructor.
	 *
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param ProductPageFactory $productPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\WeltPixel\ProductPage\Model\ProductPageFactory $productPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->jsonHelper = $jsonHelper;
		$this->productPageFactory = $productPageFactory;
		parent::__construct($context);
	}

	/**
	 * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
	 */
	public function getCollection(){
		return $this->productPageFactory->create()->getCollection();
	}

	/**
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		try {
			return $this->jsonResponse($this->getCollection());
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			return $this->jsonResponse($e->getMessage());
		} catch (\Exception $e) {
			$this->logger->critical($e);
			return $this->jsonResponse($e->getMessage());
		}
	}

	/**
	 * @param string $response
	 *
	 * @return mixed
	 */
	public function jsonResponse($response = '')
	{
		return $this->getResponse()->representJson(
			$this->jsonHelper->jsonEncode($response)
		);
	}
}