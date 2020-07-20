<?php
namespace WeltPixel\Quickview\Block\Widget;

class Hotspot extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/hotspot.phtml');
    }

    /**
     * @return integer
     */
    public function getProductId() {
        $product = $this->getData('product');
        $value = explode('/', $product);
        $productId = false;

        if (isset($value[0]) && isset($value[1]) && $value[0] == 'product') {
            $productId = $value[1];
        }

        return $productId;
    }

    /**
     * @return string
     */
    public function getPositionCss() {
        $css = '';
        $positionTop = trim($this->getData('position_top'));
        $positionLeft = trim($this->getData('position_left'));
        $positionCss = [];

        if ($positionTop) {
            $positionCss[] = 'top:' . $positionTop . '%';
        }
        if ($positionLeft) {
            $positionCss[] = 'left: ' . $positionLeft . '%';
        }

        if ($positionCss) {
            $css = implode(";", $positionCss) . ';';
        }

        return $css;
    }

    /**
     * @return string
     */
    public function getColorCss() {
        $css = '';
        $color = trim($this->getData('color'));

        if ($color) {
            $css =  'background-color:' . $color . ';';
        }

        return $css;
    }

    /**
     * @return string
     */
    public function getBgColorCss() {
        $css = '';
        $bgColor = trim($this->getData('bg_color'));

        if ($bgColor) {
            $css = 'background-color:' . $bgColor . ';';
        }

        return $css;
    }

    /**
     * @param sting $uniqId
     * @return string
     */
    public function getStyleForHotspot($uniqId) {
        $style =  $this->getColorCss() . $this->getBgColorCss();
        if ($style) {
            $style = '<style>'
                . $uniqId . ' li { ' . $this->getPositionCss() . '} '
                . $uniqId . ' li a { ' . $this->getBgColorCss() . '} '
                . $uniqId . ' li a::before,' . $uniqId . ' li a::after { ' . $this->getColorCss() . '} '
                . '</style>';
        }

        return $style;
    }

}
