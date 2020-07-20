<?php

namespace Cminds\SupplierInventoryUpdate\Model\Update;

use Magento\Framework\Model\Context;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Registry;
use Cminds\SupplierInventoryUpdate\Helper\Data as UpdaterHelper;

class Csv extends AbstractUpdate
{
    protected $moduleReader;

    protected $feedUrl;
    protected $matchinCsvColumn;
    protected $matchingPos;
    protected $matchingProductAttribute;
    protected $matchingQtyColumn;
    protected $columnPos;
    protected $csvAction;
    protected $matchingCostColumn;
    protected $matchingColumnIndex;
    protected $delimiter;

    public function __construct(
        Context $context,
        Registry $registry,
        UpdaterHelper $updateHelper,
        Reader $moduleReader
    ) {
        $this->moduleReader = $moduleReader;

        parent::__construct(
            $context,
            $registry,
            $updateHelper
        );
    }

    /**
     * Parse Csv to prepare it to import
     *
     * @return parsed data
     */
    public function parse()
    {
        if (!$this->feedUrl) {
            return false;
        }

        if (!$this->matchinCsvColumn) {
            return false;
        }

        $url = $this->feedUrl;

        //$content = file_get_contents($url);
        $content = file_get_contents('medplus.csv');

        $nPos = strpos($content, "\n");
        $headers = substr($content, 0, $nPos);
        $headers = explode($this->getDelimiter(), $headers);

        $i = 0;
        foreach ($headers as &$header) {
            $header = str_replace('"', '', $header);
            $header = str_replace("\r", '', $header);

            if (strtolower($header) == strtolower($this->matchinCsvColumn)) {
                $this->matchingPos = $i;
            }

            if ($this->matchingColumnIndex) {
                if (strtolower($header) == strtolower($this->matchingColumnIndex)) {
                    $this->columnPos = $i;
                }
            }
            $i++;
        }

        $content = substr($content, $nPos, strlen($content));
        $rows = explode("\n", $content);
        unset(
            $content,
            $header,
            $i,
            $nPos,
            $url
        );

        $data = [];
        foreach ($rows as $row) {
            if ($row == "") {
                continue;
            }

            $row = str_replace("\r", '', $row);
            $cols = str_getcsv($row, $this->getDelimiter());
            /*if (count($headers) !== count($cols)) {
                continue;
            }

            $cols = array_combine($headers, $cols);*/

            $data[] = $cols;
        }

        $this->setParsedData($data);
    }

    /**
     * Get delimiter for given CSV if not set.
     *
     * @return string
     */
    protected function getDelimiter()
    {
        $delimiter = $this->delimiter;

        if (!$delimiter) {
            $delimiter = ',';
        }

        return $delimiter;
    }

    protected function notify()
    {
        return $this;
    }
}
