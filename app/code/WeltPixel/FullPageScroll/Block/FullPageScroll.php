<?php
namespace WeltPixel\FullPageScroll\Block;

use Magento\Cms\Helper as cmsHelper;
use Magento\Store\Model\ScopeInterface;
/**
 * Class FullPageScroll
 * @package WeltPixel\FullPageScroll\Block
 */
class FullPageScroll extends \Magento\Framework\View\Element\Template {

	/**
	 * @var \Magento\Cms\Api\PageRepositoryInterface
	 */
	protected $pageRepository;

	/**
	 * @var \Magento\Cms\Model\Template\FilterProvider
	 */
	protected $_filterProvider;

	/**
	 * @var string
	 */
	protected $_scopeStore = ScopeInterface::SCOPE_STORE;

	/**
	 * @var string
	 */
	protected $cmsBlockPrefix = 'fullpagescroll';

	/**
	 * FullPageScroll constructor.
	 *
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory
	 * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
	 * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory,
		\Magento\Cms\Api\PageRepositoryInterface $pageRepository,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		array $data = []
	) {
		$this->blockColFactory = $blockColFactory;
		$this->pageRepository  = $pageRepository;
		$this->_filterProvider = $filterProvider;
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$page = $om->get('Magento\Framework\View\Page\Config');
		$page->addPageAsset('WeltPixel_FullPageScroll::css/jquery.fullPage.css');
		$page->addPageAsset('WeltPixel_FullPageScroll::css/custom.css');
		parent::__construct( $context, $data );
	}

	/**
	 * @return mixed
	 */
	public function enable()
	{
		return $this->_scopeConfig->getValue('weltpixel_fullpagescroll/general/enable', $this->_scopeStore);
	}

	/**
	 * @return string
	 */
	private function getCurrentPageUrlKey() {
		try {
			$pageId = $this->_request->getParam( 'page_id', $this->_request->getParam( 'id', false ) );
			$page   = $this->pageRepository->getById( $pageId );

			return $page->getIdentifier();
		} catch ( \Magento\Framework\Exception\NoSuchEntityException $e ) {
			if($this->getStoreCode() == 'default'){
				return 'home';
			} else {
				return 'home_' . $this->getStoreCode();
			}
		}
	}

	/**
	 * @return int
	 */
	private function getStoreId() {
		return $this->_storeManager->getStore()->getId();
	}

	/**
	 * @return int
	 */
	private function getStoreCode() {
		return $this->_storeManager->getStore()->getCode();
	}

	/**
	 * @return \Magento\Cms\Model\ResourceModel\Block\Collection
	 */
	private function getCmsBlockCollection() {
		$data = $this->blockColFactory->create();

		return $data;
	}

	/**
	 * @param $content
	 * @param $i
	 *
	 * @return string
	 */
	private function htmlTemplate( $content, $i, $backgroundImage ) {
		if($backgroundImage != false){
			$backgroundImage = ' style="background: url(' . $backgroundImage . ') no-repeat center / cover;"';
		} else {
			$backgroundImage = '';
		}
		return '
			<div class="section" id="section' . $i . '"' . $backgroundImage . '>
				<div class="overlay">
					<div>
						' . $content . '
					</div>
				</div>
			</div>
		';
	}

	/**
	 * @return array
	 */
	public function cmsBlockFiltered() {
		$currentUrl = $this->getCurrentPageUrlKey();
		$prefix = $this->cmsBlockPrefix;
		$blocks = $this->getCmsBlockCollection()
		             ->addFieldToFilter( 'identifier', [ 'like' => $prefix . '_' . $currentUrl . '_%' ] )
		             ->addFieldToFilter( 'is_active', 1 )
		             ->addStoreFilter( $this->getStoreId() )
					 ->setOrder( 'identifier', 'ASC' )
		             ->getData();

		$i = 0;
        $section = [];
		foreach ( $blocks as $block ) {
			$content = $this->_filterProvider->getPageFilter()->filter($block['content']);
			$_img_url = preg_match( '/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $img );
			$contentWitoutFirstImage = preg_replace('/<img(.*)>/i','',$content, 1);

			if ( $_img_url ){
				$section[] = $this->htmlTemplate( $contentWitoutFirstImage, $i, $img[1] );
			} else {
				$section[] = $this->htmlTemplate( $content, $i, false );
			}

			$i ++;
		}

		return $section;
	}
}
