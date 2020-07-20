<?php

namespace WeltPixel\FrontendOptions\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 *
 * @api
 * @since 100.0.2
 */
class ContactForm extends \Magento\Contact\Block\ContactForm
{
    protected $_wpHelper;
    protected $_blockCollectionFactory;
    protected $_filterProvider;
    protected $_blockFactory;
    const SAMPLE_TOP_IMAGE = 'WeltPixel_FrontendOptions::images/sample.png';
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \WeltPixel\FrontendOptions\Helper\Data $wpHelper,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_wpHelper = $wpHelper;
        $this->_blockCollectionFactory = $blockCollectionFactory;
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getTopImage()
    {
        $imagePath = $this->_wpHelper->getTopImage();
        if (!$imagePath) {
            return $this->getSampleImage();
        }
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $customFolder = \WeltPixel\FrontendOptions\Model\Config\Backend\Image::UPLOAD_DIR . '/';
        return ($imagePath) ? $mediaUrl . $customFolder . $imagePath : false;
    }

    public function getSampleImage()
    {
        $designParams = $this->getDesignParams();
        return $this->_assetRepo->getUrlWithParams(
            self::SAMPLE_TOP_IMAGE,
            $designParams
        );
    }

    /**
     * @return array
     */
    private function getDesignParams()
    {
        return [
            'area' => $this->_design->getArea(),
            'theme' => $this->_design->getDesignTheme()->getCode(),
            'themeModel' => $this->_design->getDesignTheme(),
            'locale' => $this->_design->getLocale(),
        ];
    }
    /**
     * @return mixed
     */
    public function isEnableBlock()
    {
        return $this->_wpHelper->isEnabledBlock();
    }

    /**
     * @return string
     */
    public function getContactBlockId()
    {
        return $this->_wpHelper->getContactBlockId();
    }

    /**
     * @return string
     */
    public function getBlockHtmlAfterId()
    {
        $blockId = $this->getContactBlockId();
        $html = '';
        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);

            $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
        }
        return   $html;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $templateFile = 'WeltPixel_FrontendOptions::contact_form_' . $this->_wpHelper->getContactPageVersion() . '.phtml';
        return $templateFile;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Contact\Block\ContactForm'));
        return parent::_toHtml();
    }
}
