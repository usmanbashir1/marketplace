<?php
namespace Cminds\Supplierfrontendproductuploader\Model\Data;

use Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Cminds Supplierfrontendproductuploader Sources interface.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

class Result extends AbstractSimpleObject implements ResultInterface
{

    /**
     * {@inheritdoc}
     */
    public function getResultKey(){
        return $this->_get(self::RESULT_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setResultKey($data){
        $this->setData(self::RESULT_KEY, $data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultData(){
        return $this->_get(self::RESULT_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setResultData($data){
        $this->setData(self::RESULT_DATA, $data);
        return $this;
    }
}
