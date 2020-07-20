<?php
namespace Cminds\Supplierfrontendproductuploader\Api\Data;

/**
 * Cminds Supplierfrontendproductuploader Sources interface.
 * @api
 * @category Cminds
 * @package  Cminds_Supplierfrontendproductuploader
 */

interface TokenInterface
{
    /**
     * Entity data keys.
     */
    const ENTITY_ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const TOKEN = 'customer_token';


    /**
     * Get entity id.
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity id.
     *
     * @param int $entity_id
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\TokenInterface
     */
    public function setEntityId($entity_id);

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set entity id.
     *
     * @param int $customer_id
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\TokenInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get source code.
     *
     * @return string
     */
    public function getCustomerKey();

    /**
     * Set source code.
     *
     * @param string $key
     *
     * @return \Cminds\Supplierfrontendproductuploader\Api\Data\TokenInterface
     */
    public function setCustomerKey($key);
}
