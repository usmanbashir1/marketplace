<?php
namespace WeltPixel\Quickview\Block\Adminhtml\Widget;

Class InfoMessage extends \Magento\Backend\Block\Template
{
    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $infoMessage = '<div class="wp-widget-info-message col-xs-12">
<div class="col-xs-12 with-border">Please make sure to add the widget into a div with the class name &quot;wp-hotspot-quickview&quot; that contains the image tag and the  hotspot quickview widget.<br/>
ex:</div>
<div class="col-xs-12">&lt;div class=&quot;wp-hotspot-quickview&quot;&gt;<br/>
&lt;img src=&quot;my_image.jpg&quot; style=&quot;width: 100%&quot; alt=&quot;my image&quot;&gt;<br/>
{{widget insert here}}<br/>
&lt;/div&gt;</div>';
        $customStyle = "<style>.wp-widget-info-message {padding: 10px; background-color: #fafafa; border: 3px solid #eb5202} .wp-widget-info-message div {padding: 10px;} .wp-widget-info-message div.with-border{border-bottom: 1px solid}</style>";
        $element->setData('after_element_html',  $customStyle . $infoMessage);
        return $element;
    }
}