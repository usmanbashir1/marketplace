<?php
namespace Cminds\Supplierfrontendproductuploader\Api\Data;

/**
 * Cminds Supplierfrontendproductuploader Result Interface.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

interface ResultInterface
{
    /**
     * Entity data keys.
     */
    const RESULT_KEY = 'result_key';
    const RESULT_DATA = 'result_data';
    const RESULT_PRODUCTS = 'result_products';

    /**
     * Get data key.
     *
     * @return string|null
     */
    public function getResultKey();

    /**
     * Set data key.
     *
     * @param string $data
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface
     */
    public function setResultKey($data);

    /**
     * Get data.
     *
     * @return string[]|null
     */
    public function getResultData();

    /**
     * Set data.
     *
     * @param string[] $data
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\ResultInterface
     */
    public function setResultData($data);
}
