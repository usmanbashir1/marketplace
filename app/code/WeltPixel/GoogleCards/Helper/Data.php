<?php

namespace WeltPixel\GoogleCards\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_cardsOptions;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);

        $this->_cardsOptions = $this->scopeConfig->getValue('weltpixel_google_cards', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isEnabled($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['enable'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDescriptionType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['description'];
        }
    }

    public function getConfigItemCondition($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/condition', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['condition'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getConfigReviewsFormat($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/reviews_format', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['reviews_format'];
        }
    }

    public function getConfigNumberOfReviews($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/reviews_count', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['reviews_count'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBrand($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/brand', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['brand'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSku($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/sku', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['sku'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMpn($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/mpn', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['mpn'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGtin($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/gtin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['gtin'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTwitterCardDescriptionType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/twitter_cards/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['twitter_cards']['description'];
        }
    }


    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterCardType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/twitter_cards/card_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['twitter_cards']['card_type'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterCreator($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/twitter_cards/creator', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['twitter_cards']['creator'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTwitterSite($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/twitter_cards/site', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['twitter_cards']['site'];
        }
    }


    /**
     * @return string
     */
    public function getTwitterShippingCountry()
    {
        return $this->scopeConfig->getValue('shipping/origin/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookDescriptionType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/facebook_opengraph/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['facebook_opengraph']['description'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookSiteName($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/facebook_opengraph/site_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['facebook_opengraph']['site_name'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookAppId($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/facebook_opengraph/app_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['facebook_opengraph']['app_id'];
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getFacebookRetailerId($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/facebook_opengraph/retailer_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['facebook_opengraph']['retailer_id'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGoogleCardsPrice($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/general/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['general']['price'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getRichSnippetSearchConfiguration($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_search/enable_search', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_search']['enable_search'];
        }
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTwitterCardsPrice($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/twitter_cards/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['twitter_cards']['price'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFacebookOpenGraphPrice($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/facebook_opengraph/price', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['facebook_opengraph']['price'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDefaultLogoConfig($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_logo/mage_logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_logo']['mage_logo'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCustomLogoConfig($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_logo/google_logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {

            return $this->_cardsOptions['rich_snippet_logo']['google_logo'];
        }
    }

    /**
     * @param int $storeId )
     * @return mixed
     */
    public function getPhone($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_contact/contact_telephone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_contact']['contact_telephone'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContactType($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_contact/contact_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_contact']['contact_type'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContactArea($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_contact/contact_area', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_contact']['contact_area'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContactOption($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_contact/contact_option', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_contact']['contact_option'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getContactLanguage($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_contact/contact_language', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_contact']['contact_language'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFacebookUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/facebook_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['facebook_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getTwitterUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/twitter_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['twitter_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getGooglePlusUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/google_plus_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['google_plus_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getInstagramUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/instagram_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['instagram_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getYoutubeUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/youtube_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['youtube_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getLinkedinUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/linkedin_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['linkedin_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMyspaceUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/myspace_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['myspace_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPinterestUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/pinterest_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['pinterest_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSoundcloudUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/soundcloud_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['soundcloud_url'];
        }
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getThumblrUrlConf($storeId = 0)
    {
        if ($storeId) {
            return $this->scopeConfig->getValue('weltpixel_google_cards/rich_snippet_social_profile/tumblr_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->_cardsOptions['rich_snippet_social_profile']['tumblr_url'];
        }
    }

}
