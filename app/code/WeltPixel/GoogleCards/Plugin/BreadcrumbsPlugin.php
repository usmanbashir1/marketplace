<?php

namespace WeltPixel\GoogleCards\Plugin;

use \Magento\Theme\Block\Html\Breadcrumbs;

/**
 * Class BreadcrumbsPlugin
 * @package WeltPixel\GoogleCards\Plugin
 */
class BreadcrumbsPlugin
{
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var array
     */
    protected $breadcrumbsData = [];

    /**
     * BreadcrumbsPlugin constructor.
     * @param \Magento\Catalog\Model\Session $catalogSession
     */
    public function __construct(
        \Magento\Catalog\Model\Session $catalogSession
    )
    {
        $this->_catalogSession = $catalogSession;
    }

    /**
     * @param Breadcrumbs $breadcrumbs
     * @param $crumbName
     * @param $crumbInfo
     * @return array
     */
    public function beforeAddCrumb(Breadcrumbs $breadcrumbs, $crumbName, $crumbInfo)
    {
        if (isset($crumbInfo['label']) && (isset($crumbInfo['link']) && !empty($crumbInfo['link']))) {
            $crumbData = [];
            $label = is_object($crumbInfo['label']) ? $crumbInfo['label']->getText() : $crumbInfo['label'];
            $crumbData['label'] = $label;
            $crumbData['link'] = $crumbInfo['link'];
            if (!in_array($label, array_column($this->breadcrumbsData, 'label'))) {
                array_push($this->breadcrumbsData, $crumbData);
            }

        }
        $this->_catalogSession->setBreadcrumbData($this->breadcrumbsData);
        return [
            $crumbName,
            $crumbInfo,
        ];
    }

}
