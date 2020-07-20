<?php
namespace WeltPixel\FrontendOptions\Plugin;

class ViewPreProcessorChain extends ViewPreprocessorAbstract {

    /**
     * @param \Magento\Framework\View\Asset\PreProcessor\Chain $subject
     * @param $result
     * @return mixed
     */
    public function afterGetContent(
        \Magento\Framework\View\Asset\PreProcessor\Chain $subject, $result)
    {
       return $this->_getContent($subject, $result);
    }

}