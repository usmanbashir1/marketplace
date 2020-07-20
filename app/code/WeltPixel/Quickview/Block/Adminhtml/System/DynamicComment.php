<?php
namespace WeltPixel\Quickview\Block\Adminhtml\System;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\View\Element\AbstractBlock;

class DynamicComment extends AbstractBlock implements CommentInterface
{
    /**
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        $url = $this->_urlBuilder->getUrl('weltpixelquickview/custommessages');
        return __("If set to Yes, you can use Custom Dynamic Messages based on rules. Dynamic messages have priority over Global messages. Go to <a target='_blank' href='$url'>grid</a> and add your Dynamic Custom Messages.");
    }
}
