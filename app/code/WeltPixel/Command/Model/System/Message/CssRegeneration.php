<?php
namespace WeltPixel\Command\Model\System\Message;


class CssRegeneration implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected  $_session;


    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Session $session
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_session = $session;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return md5('weltpixel_css_regeneration');
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if ($this->_session->getWeltPixelCssRegeneration()) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $url = $this->_urlBuilder->getUrl('adminhtml/cache');
        $message = __('Please regenerate Pearl Theme LESS/CSS files from <a href="%1">Cache Management Section</a>', $url);
        return $message;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL;
    }
}
