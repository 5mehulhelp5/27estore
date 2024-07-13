<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SampleProduct
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SampleProduct\Api\Data;

interface SampleProductOrderInterface
{
    public const ENTITY_ID = 'entity_id';
    public const SAMPLE_ID = 'sample_id';
    public const ORDER_ID = 'order_id';
    public const CUSTOMER_ID = 'customer_id';
    public const QTY = 'qty';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setId($id);

    /**
     * Get Sample ID.
     *
     * @return int|null
     */
    public function getSampleId();

    /**
     * Set Sample ID.
     *
     * @param int $sampleId
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setSampleId($sampleId);

    /**
     * Get Order ID.
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set Order ID.
     *
     * @param int $orderId
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setOrderId($orderId);

    /**
     * Get Customer ID.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Customer ID.
     *
     * @param int $customerId
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get Qty.
     *
     * @return int|null
     */
    public function getQty();

    /**
     * Set Qty.
     *
     * @param int $qty
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setQty($qty);

    /**
     * Get Status.
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status.
     *
     * @param int $status
     *
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setStatus($status);

    /**
     * Gets creation timestamp.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Sets creation timestamp.
     *
     * @param string $timestamp
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setCreatedAt($timestamp);

    /**
     * Gets updated_at timestamp.
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Sets updated_at timestamp.
     *
     * @param string $timestamp
     * @return \Webkul\SampleProduct\Api\Data\SampleProductOrderInterface
     */
    public function setUpdatedAt($timestamp);
}
