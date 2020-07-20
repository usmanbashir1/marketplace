<?php

namespace WeltPixel\GoogleCards\Block;

class RichSnippet extends GoogleCards
{

    /**
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getRichSnippetPrice();
        return $this->_calculatePrice($priceOption);
    }

    /**
     * @return mixed
     */
    public function isSearchEnabled()
    {
        return $this->_helper->getRichSnippetSearchConfiguration();
    }

    /**
     * @return bool|string
     */
    public function getLogoUrl()
    {
        $defaultLogo = $this->_helper->getDefaultLogoConfig();
        $customLogo = $this->_helper->getCustomLogoConfig();
        if ($defaultLogo) {
            return $this->_logo->getLogoSrc();
        }
        if (!$customLogo) {
            return false;
        }
        $logoPath = parent::CUSTOM_LOGO_PATH . DIRECTORY_SEPARATOR . $customLogo;
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . DIRECTORY_SEPARATOR . $logoPath;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->_helper->getPhone();
    }

    /**
     * @return mixed
     */
    public function getContactType()
    {
        return $this->_helper->getContactType();
    }

    /**
     * @return mixed
     */
    public function getContactOption()
    {
        return $this->_helper->getContactOption();
    }

    /**
     * @return mixed
     */
    public function getContactArea()
    {
        return $this->_helper->getContactArea();
    }

    /**
     * @return mixed
     */
    public function getContactLanguage()
    {
        return $this->_helper->getContactLanguage();
    }
}