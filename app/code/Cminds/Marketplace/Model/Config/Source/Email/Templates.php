<?php

namespace Cminds\Marketplace\Model\Config\Source\Email;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Email\Model\Template\Config;

/**
 * Class Templates
 *
 * @package Cminds\Marketplace\Model\Config\Source\Email
 */
class Templates extends AbstractSource
{
    /**
     * @var Config
     */
    protected $_emailConfig;

    /**
     * @param Config $emailConfig
     */
    public function __construct(
        Config $emailConfig
    ) {
        $this->_emailConfig = $emailConfig;
    }


    /**
     * Returns collection of all available templates
     *
     * @return array[]
     */
    public function getConfigTemplates()
    {
        return $this->_emailConfig->getAvailableTemplates();
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = [];
        foreach ($this->getConfigTemplates() as $configTemplate) {
            if (stripos($configTemplate['value'], 'invoice_template')) {
                $options[] = $configTemplate;
            }
        }
        return $options;
    }


    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
