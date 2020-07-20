<?php
namespace Cminds\MultipleProductVendors\Plugin\Checkout\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Model\Session;


class Cart
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Plugin constructor.
     *
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->quote = $checkoutSession->getQuote();
    }

    /**
     * beforeAddProduct
     * prevent main product from being added to the cart
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        if ( $productInfo->hasData('main_product')
            && true === (bool) $productInfo->getData('main_product') ) {
                throw new LocalizedException(__('Could not add Product to Cart'));
        }
        return [$productInfo, $requestInfo];
    }
}