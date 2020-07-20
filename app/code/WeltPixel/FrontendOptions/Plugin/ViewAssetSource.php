<?php
namespace WeltPixel\FrontendOptions\Plugin;

class ViewAssetSource extends ViewPreprocessorAbstract {

    /**
     * @param \Magento\Framework\View\Asset\Source $subject
     * @param $result
     * @return mixed
     */
    public function afterGetContent(
        \Magento\Framework\View\Asset\Source $subject, $result)
    {
       return $this->_getContent($subject, $result);
    }

}