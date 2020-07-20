<?php

namespace WeltPixel\FullPageScroll\Plugin;

class Utility extends \WeltPixel\Backend\Plugin\Utility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '70', '117', '108', '108', '80',
                '97', '103', '101', '83', '99', '114', '111', '108', '108'
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
                    '101', '108', '116', '112', '105', '120', '101', '108', '95', '102', '117', '108', '108', '112',
                    '97', '103', '101', '115', '99', '114', '111', '108', '108',
                ]
            )
        ];
    }

}
