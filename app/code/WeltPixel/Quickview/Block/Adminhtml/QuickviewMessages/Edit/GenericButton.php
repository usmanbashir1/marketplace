<?php
namespace WeltPixel\Quickview\Block\Adminhtml\QuickviewMessages\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \WeltPixel\Quickview\Model\QuickviewMessagesFactory
     */
    protected $quickviewMessagesFactory;

    /**
     * @param Context $context
     * @param \WeltPixel\Quickview\Model\QuickviewMessagesFactory $quickviewMessagesFactory
     */
    public function __construct(
        Context $context,
        \WeltPixel\Quickview\Model\QuickviewMessagesFactory $quickviewMessagesFactory
    ) {
        $this->context = $context;
        $this->quickviewMessagesFactory = $quickviewMessagesFactory;
    }

    /**
     * Return item ID
     *
     * @return int|null
     */
    public function getCustomMessageId()
    {
        try {
            /** @var \WeltPixel\Quickview\Model\QuickviewMessages $quickviewMessage */
            $quickviewMessage = $this->quickviewMessagesFactory->create();
            return $quickviewMessage->load($this->context->getRequest()->getParam('id'))->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
