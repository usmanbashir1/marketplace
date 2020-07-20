<?php
namespace WeltPixel\Command\Block\Cache;

class Additional extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function getCssGenerationUrl()
    {
        return $this->getUrl('weltpixelcommand/cache/generateCss');
    }

    /**
     * @return array
     */
    public function getStoreViews() {
        $storeCodes = [];
        $websites = $this->_storeManager->getWebsites();

        foreach ($websites as $website) {
            $websiteName = $this->_escaper->escapeHtml($website->getName());
            $groups = $website->getGroups();
            foreach ($groups as $group) {
                $storeName = $this->_escaper->escapeHtml($group->getName());
                $stores = $group->getStores();
                foreach ($stores as $storeView) {
                    $storeViewName = $this->_escaper->escapeHtml($storeView->getName());
                    $storeCodes[] = [
                        'value' => $storeView->getCode(),
                        'label' => $websiteName . ' > ' . $storeName . ' > ' . $storeViewName
                    ];
                }
            }
        }

        if (count($storeCodes) > 1) {
            array_unshift($storeCodes, [
                'value' => 0,
                'label' => __('Please select store view')
            ], [
                'value' => '-',
                'label' => __('ALL STORE VIEWS')
            ]);
        }

        return $storeCodes;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getAuthorization()->isAllowed('WeltPixel_Command::LessCssGeneration')) {
            return '';
        }
        return parent::_toHtml();
    }
}
