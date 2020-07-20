<?php

namespace WeltPixel\Quickview\Model\Config\Backend;

/**
 * Class Loading
 * @package WeltPixel\Quickview\Model\Config\Backend
 */
class Loading extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
