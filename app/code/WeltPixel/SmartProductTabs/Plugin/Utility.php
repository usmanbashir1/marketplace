<?php

namespace WeltPixel\SmartProductTabs\Plugin;

class Utility extends \WeltPixel\Backend\Plugin\Utility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '83', '109', '97', '114', '116',
                '80', '114', '111', '100', '117', '99', '116', '84', '97', '98', '115'
            ]
        );
    }

    /**
     * @return array
     */
    protected function _getAdminPaths()
    {
        return [
            $this->convertToString(
                [
                    '115', '121', '115', '116', '101', '109', '95', '99', '111', '110', '102', '105', '103', '47',
                    '101', '100', '105', '116', '47', '115', '101', '99', '116', '105', '111', '110', '47', '119',
                    '101', '108', '116', '112', '105', '120', '101', '108', '95', '115', '109', '97', '114', '116',
                    '112', '114', '111', '100', '117', '99', '116', '116', '97', '98', '115'
                ]
            )
        ];
    }

}