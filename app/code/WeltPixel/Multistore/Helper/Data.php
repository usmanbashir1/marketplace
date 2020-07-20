<?php

namespace WeltPixel\Multistore\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ACTIVE_PLACEHOLDER = 'WeltPixel_Multistore::images/active_placeholder.svg';
    const INACTIVE_PLACEHOLDER = 'WeltPixel_Multistore::images/inactive_placeholder.svg';

	/**
	 * @var array
	 */
	protected $_multistoreOptions;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $_currentStore;


    /**
     * @var \Magento\Framework\View\Asset\Repository;
     */
    protected $_assetRepo;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \use Magento\Framework\View\Asset\Repository $assetRepo;
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\View\Asset\Repository $assetRepo
	) {
		parent::__construct($context);

        $this->_storeManager = $storeManager;
        $this->_currentStore = $storeManager->getStore();
		$this->_multistoreOptions = $this->scopeConfig->getValue('weltpixel_multistore', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->_assetRepo = $assetRepo;
	}

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getIsEnabled($storeId = 0) {
        if ($storeId) {
            return  $this->scopeConfig->getValue('weltpixel_multistore/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['enable'];
        }
    }

	/**
	 * @param int $storeId
	 * @return mixed
	 */
	public function getStoreImage($storeId = 0) {
		if ($storeId) {
			return $this->scopeConfig->getValue('weltpixel_multistore/general/store_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		} else {
			return $this->_multistoreOptions['general']['store_image'];
		}
	}

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getStoreInactiveImage($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/store_image_inactive', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['store_image_inactive'];
        }
    }

    /**
     * @param $storeId
     * @return string
     */
	public function getImageUrl($storeId) {
        $image = $this->getStoreImage($storeId);

        if ($image) {
            $imagePath = 'weltpixel/multistore/logo/' . $image;
            $imageUrl = $this->_currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $imageUrl . $imagePath;
        } else {
            return $this->_assetRepo->getUrl(self::ACTIVE_PLACEHOLDER);
        }
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getInactiveImageUrl($storeId) {
        $image = $this->getStoreInactiveImage($storeId);

        if ($image) {
            $imagePath = 'weltpixel/multistore/logo_inactive/' . $image;
            $imageUrl = $this->_currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            return $imageUrl . $imagePath;
        } else {
            return $this->_assetRepo->getUrl(self::INACTIVE_PLACEHOLDER);
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function displayInOneRow($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/one_row', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['one_row'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBarBgColor($storeId = 0) {
        if(!$this->displayInOneRow()){
            return false;
        }
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/bar_bg_color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['bar_bg_color'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBarWidth($storeId = 0) {
        if(!$this->displayInOneRow()){
            return false;
        }
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/bar_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['bar_width'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function redirectToHomePage($storeId = 0) {
        if(!$this->getIsEnabled($storeId)) {
           return false;
        }
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/redirect_to_home_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['redirect_to_home_page'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isStoreVisible($storeId = 0) {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_multistore/general/visibility', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_multistoreOptions['general']['visibility'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed|string
     */
    public function getImageHeight($storeId = 0) {
        $imgHeight = null;
        if ($storeId) {
            $imgHeight = (int) $this->scopeConfig->getValue('weltpixel_multistore/general/img_height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $imgHeight = (int) $this->_multistoreOptions['general']['img_height'];
        }
    
        $imgHeight = $imgHeight && is_integer($imgHeight) ? $imgHeight . 'px' : '39px';

        return $imgHeight;
    }

    /**
     * @param int $storeId
     * @return mixed|string
     */
    public function getImageWidth($storeId = 0) {
        $imgWidth = null;
        if ($storeId) {
            $imgWidth = (int) $this->scopeConfig->getValue('weltpixel_multistore/general/img_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $imgWidth = (int) $this->_multistoreOptions['general']['img_width'];
        }
    
        return $imgWidth && is_integer($imgWidth) ? $imgWidth . 'px' : 'auto';
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getExcludedStores($storeId = 0) {
        $result = [];
        $excludedList = '';
        if ($storeId) {
            $excludedList = $this->scopeConfig->getValue('weltpixel_multistore/general/exclude_stores', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $excludedList = $this->_multistoreOptions['general']['exclude_stores'];
        }

        if ($excludedList) {
            $result = explode(',', $excludedList);
        }

        return $result;
    }

    /**
     * @param int $storeId
     * @param $storeCode
     * @return bool
     */
    public function canStoreBeDisplayed($storeId = 0, $storeCode) {

        $isVisible = $this->isStoreVisible($storeId);
        if (!$isVisible) {
            return false;
        }

        $excludedStores = $this->getExcludedStores();
        if (in_array($storeCode, $excludedStores)) {
            return false;
        }

        return true;
    }
}
