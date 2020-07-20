<?php

namespace WeltPixel\Sitemap\Plugin;

class Utility extends \WeltPixel\Backend\Plugin\Utility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '83', '105', '116', '101',
                '109', '97', '112'
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
                    '119', '101', '108', '116', '112', '105', '120', '101', '108', '115', '105', '116', '101', '109',
                    '97', '112', '47', '115', '105', '116', '101', '109', '97', '112'
                ]
            )
        ];
    }
}