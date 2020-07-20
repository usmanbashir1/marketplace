<?php

namespace Cminds\DropshipNotification\Model\Config\Source;

use Magento\Config\Model\Config\Source\Email\Template;

class EmailTemplate extends Template
{
    /**
     * Get list of custom email templates.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_templatesFactory->create();
        $collection->load();

        $options = [
            ['value' => 'cminds_dropshipnotification_notification', 'label' => __('Default Email')]
        ];

        foreach ($collection as $template) {
            $options[] = [
                'value' => $template->getTemplateId(),
                'label' => __($template->getTemplateCode())
            ];
        }

        return $options;
    }
}