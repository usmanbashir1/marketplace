<?php
namespace WeltPixel\ThankYouPage\Block;

/**
 * Class Newsletter
 * @package WeltPixel\ThankYouPage\Block
 */
class Newsletter extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscriber;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \WeltPixel\ThankYouPage\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \WeltPixel\ThankYouPage\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \WeltPixel\ThankYouPage\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->subscriber = $subscriber;
    }

    /**
     * Retrieve current email address
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getEmailAddress()
    {
        return $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('wpthankyoupage/newsletter/subscribe', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->helper->getNewsletterSubscribeDescription();
    }

    /**
     * Render the content of the block only if user is not subscribed
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $subscriberModel = $this->subscriber->loadByEmail($this->getEmailAddress());

        if ($subscriberModel->isSubscribed()) {
            return '';
        }

        return parent::_toHtml();
    }
}
