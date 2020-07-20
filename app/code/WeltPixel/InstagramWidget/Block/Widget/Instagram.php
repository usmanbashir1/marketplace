<?php
namespace WeltPixel\InstagramWidget\Block\Widget;

class Instagram extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @return string
     */
    public function getTemplate()
    {
        $instagramApiType = $this->getData('instagram_api_type');
        switch ($instagramApiType) {
            case 'javascript_parser':
                $template = 'widget/js/instagram_widget.phtml';
                break;
            default:
                $template = 'widget/instagram_widget.phtml';
                break;
        }

        $this->setTemplate($template);
        return parent::getTemplate();
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function isLazyLoadEnabled() {
        return $this->_scopeConfig->getValue('weltpixel_lazy_loading/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param int $storeId
     * @return mixed|string
     */
    public function getLazyLoadPlaceholderWidth() {
        $imgWidth = null;
        $imgWidth = (int) $this->_scopeConfig->getValue('weltpixel_lazy_loading/advanced/placeholder_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $imgWidth && is_integer($imgWidth) ? $imgWidth . 'px' : 'auto';
    }
}
