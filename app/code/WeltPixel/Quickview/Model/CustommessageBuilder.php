<?php

namespace WeltPixel\Quickview\Model;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use WeltPixel\Quickview\Helper\Data as QuickviewHelper;
use WeltPixel\Quickview\Model\ResourceModel\QuickviewMessages\CollectionFactory as QuickviewMessagesCollectionFactory;


/**
 * Class CustommessageBuilder
 * @package WeltPixel\Quickview\Model
 */
class CustommessageBuilder
{
    const CUSTOM_MESSAGE_ENABLED = 'weltpixel_quickview/custom_message/enable';
    const DYNAMIC_CUSTOM_MESSAGE_ENABLED = 'weltpixel_quickview/custom_message/enable_dynamic';

    /**
     * Date
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $indexTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var QuickviewMessagesCollectionFactory
     */
    protected $quickviewMessagesCollectionFactory;

    /**
     * @var QuickviewMessagesFactory
     */
    protected $quickviewMessagesFactory;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var QuickviewHelper
     */
    protected $quickviewHelper;


    /**
     * @param ResourceConnection $resource
     * @param QuickviewMessagesCollectionFactory $quickviewMessagesCollectionFactory
     * @param QuickviewMessagesFactory $quickviewMessagesFactory
     * @param HttpContext $httpContext
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param QuickviewHelper $quickviewHelper
     * @param DateTime $date
     */
    public function __construct(
        ResourceConnection $resource,
        QuickviewMessagesCollectionFactory $quickviewMessagesCollectionFactory,
        QuickviewMessagesFactory $quickviewMessagesFactory,
        HttpContext $httpContext,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        QuickviewHelper $quickviewHelper,
        DateTime $date
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->quickviewMessagesCollectionFactory = $quickviewMessagesCollectionFactory;
        $this->quickviewMessagesFactory = $quickviewMessagesFactory;
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->quickviewHelper = $quickviewHelper;
        $this->date = $date;
        $this->indexTableName = 'weltpixel_quickviewmessages_rule_idx';
    }

    /**
     * @return string
     */
    public function getIndexTableName()
    {
        return $this->indexTableName;
    }

    /**
     * @return mixed|null
     */
    public function getCustomerGroupId()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreViewId()
    {
        return $this->storeManager->getStore()->getId();
    }


    /**
     * @param int $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomMessageForProduct($productId)
    {
        $customMessage = '';
        $isCustomMessageEnabled = $this->quickviewHelper->isCustomMessageEnabled();
        $isDynamicCustomMessageEnabled = $this->quickviewHelper->isDynamicCustomMessageEnabled();

        if (!$isCustomMessageEnabled) {
            return '';
        }

        $customMsg = $this->quickviewHelper->getCustomMessageContent();
        $customMsgBgColor = $this->quickviewHelper->getCustomMessageBgColor();
        $customMsgFontColor = $this->quickviewHelper->getCustomMessageFontColor();
        $customMsgFontSize = $this->quickviewHelper->getCustomMessageFontSize();
        $customMsgCustomCss = $this->quickviewHelper->getCustomMessageCustomCss();

        if ($isDynamicCustomMessageEnabled) {
            $dynamicCustomMessage = $this->getProductDynamicMessage($productId);
            if (isset($dynamicCustomMessage['custom_message'])) {
                $customMsg = $dynamicCustomMessage['custom_message'];
            }
            if (isset($dynamicCustomMessage['custom_message_bg_color'])) {
                $customMsgBgColor = $dynamicCustomMessage['custom_message_bg_color'];
            }
            if (isset($dynamicCustomMessage['custom_message_font_color'])) {
                $customMsgFontColor = $dynamicCustomMessage['custom_message_font_color'];
            }
            if (isset($dynamicCustomMessage['custom_message_font_size'])) {
                $customMsgFontSize = $dynamicCustomMessage['custom_message_font_size'];
            }
            if (isset($dynamicCustomMessage['custom_css'])) {
                $customMsgCustomCss = $dynamicCustomMessage['custom_css'];
            }
        }

        $customMessage = $this->prepareMessageForDisplay($customMsg, $customMsgBgColor, $customMsgFontColor, $customMsgFontSize, $customMsgCustomCss);

        return $customMessage;
    }

    /**
     * @param $productId
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductDynamicMessage($productId)
    {
        $indexTableName = $this->getIndexTableName();
        $indexTable = $this->resource->getTableName($indexTableName);

        $storeId = $this->getStoreViewId();
        $customerGroupId = $this->getCustomerGroupId();
        $currentTime = $this->date->gmtDate();

        $quickviewMessageCollection = $this->quickviewMessagesCollectionFactory->create();
        $quickviewMessageCollection->addFieldToFilter('main_table.status', 1);
        $quickviewMessageCollection->addFieldToFilter(
            ['main_table.store_id', 'main_table.store_id'],
            [
                ['finset' => [$storeId]],
                ['finset' => [0]]
            ]
        );
        $quickviewMessageCollection->addFieldToFilter(
            ['main_table.valid_from', 'main_table.valid_from'],
            [
                ['lteq' => $currentTime],
                ['null' =>  true]
            ]
        );
        $quickviewMessageCollection->addFieldToFilter(
            ['main_table.valid_to', 'main_table.valid_to'],
            [
                ['gteq' => $currentTime],
                ['null' =>  true]
            ]
        );
        $quickviewMessageCollection->addFieldToFilter('main_table.customer_group', ['finset' => [$customerGroupId]]);
        $quickviewMessageCollection->addFieldToFilter('idx.product_id', $productId);
        $quickviewMessageCollection->addFieldToFilter('idx.store_id', $storeId);
        $quickviewMessageCollection->getSelect()
            ->joinLeft(
                ['idx' => $indexTable],
                "main_table.id = idx.rule_id",
                []
            )
            ->order([
                'main_table.priority ASC'
            ])
            ->limit(1);

        if ($quickviewMessageCollection->getSize()) {
            return $quickviewMessageCollection->getFirstItem()->getData();
        }

        return [];
    }

    /**
     * @param $customMsg
     * @param $customMsgBgColor
     * @param $customMsgFontColor
     * @param $customMsgFontSize
     * @param $customMsgCustomCss
     * @return string
     */
    protected function prepareMessageForDisplay($customMsg, $customMsgBgColor, $customMsgFontColor, $customMsgFontSize, $customMsgCustomCss)
    {
        $result = "
        <style scoped>
    .quickview-custom-message {";
        if (strlen($customMsgBgColor)) {
             $result .= "background-color: " . $customMsgBgColor . ";";
        }
        $result .= "}";
        $result .= ".quickview-custom-message ,
    .quickview-custom-message p,
    .quickview-custom-message span,
    .quickview-custom-message a {";
        if (strlen($customMsgFontColor)) {
            $result .= "color: " . $customMsgFontColor . ";";
        }
        if (strlen($customMsgFontSize)) {
            $result .= "font-size: " . $customMsgFontSize . ";";
        }

        $result .="}";

        if (strlen($customMsgCustomCss)) {
            $result .= $customMsgCustomCss;
        }

        $result .="</style><div>" . $customMsg . "</div>";

        return $result;

    }
}
