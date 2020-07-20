<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_SocialLogin
 * @copyright   Copyright (c) 2018 WeltPixel
 * @author      WeltPixel TEAM
 */


namespace WeltPixel\SocialLogin\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class SocialLoginAccountsActions
 * @package WeltPixel\SocialLogin\Ui\Component\Listing\Column
 */
class SocialLoginAccountsActions extends Column
{
    const CUSTOMER_EDIT = 'customer/index/edit';
    /**
     * @var
     */
    public $actionUrlBuilder;
    /**
     * @var UrlInterface
     */
    public $urlBuilder;
    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */

    public $editUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href'   => $this->urlBuilder->getUrl(self::CUSTOMER_EDIT, [
                        'id' => $item['customer_id']
                    ]),
                    'label'  => __('View Customer'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
