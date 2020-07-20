<?php
namespace WeltPixel\Sitemap\Block\Adminhtml\Sitemap\Edit;

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
     * @var \WeltPixel\Sitemap\Model\SitemapFactory
     */
    protected $sitemapFactory;

    /**
     * @param Context $context
     * @param \WeltPixel\Sitemap\Model\SitemapFactory $sitemapFactory
     */
    public function __construct(
        Context $context,
        \WeltPixel\Sitemap\Model\SitemapFactory $sitemapFactory
    ) {
        $this->context = $context;
        $this->sitemapFactory = $sitemapFactory;
    }

    /**
     * Return item ID
     *
     * @return int|null
     */
    public function getSitemapId()
    {
        try {
            /** @var \WeltPixel\Sitemap\Model\Sitemap $sitemap */
            $sitemap = $this->sitemapFactory->create();
            return $sitemap->load($this->context->getRequest()->getParam('id'))->getId();
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
