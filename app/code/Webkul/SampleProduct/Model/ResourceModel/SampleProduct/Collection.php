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
namespace Webkul\SampleProduct\Model\ResourceModel\SampleProduct;

use \Webkul\SampleProduct\Model\ResourceModel\AbstractCollection;

/**
 * Webkul SampleProduct ResourceModel SampleProduct collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\SampleProduct\Model\SampleProduct::class,
            \Webkul\SampleProduct\Model\ResourceModel\SampleProduct::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Add filter by store for sample products
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(
            \Magento\Framework\DB\Select::ORDER
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_COUNT
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_OFFSET
        );
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        );

        return $select;
    }

    /**
     * Retrieve all sample_product_id for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllSampleProductIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('sample_product_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retrieve all sample_product_id for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getSampleProductId($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('sample_product_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchOne($idsSelect, $this->_bindParams);
    }

    /**
     * Retrieve sample product ordered qty for collection
     *
     * @param int $customerId
     * @return string
     */
    public function getSampleProductOrderedQty($customerId)
    {
        $sampleProductOrder = $this->getTable('wk_sample_product_order');
        
        $this->getSelect()->join(
            ['spo' => $sampleProductOrder],
            'spo.sample_id = main_table.sample_product_id'
        )
        ->where('spo.customer_id = '.$customerId)
        ->group('spo.sample_id');
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('sum(spo.qty) as ordered_qty');
        return $this->getConnection()->fetchOne($idsSelect, $this->_bindParams);
    }

    /**
     * Retrieve sample product ordered qty for collection
     *
     * @param int $customerId
     * @return string
     */
    public function getSampleProductOrderedCanceledQty($customerId)
    {
        $sampleProductOrder = $this->getTable('wk_sample_product_order');
        $salesOrderItem = $this->getTable('sales_order_item');

        $this->getSelect()->join(
            ['spo2' => $sampleProductOrder],
            'spo2.sample_id = main_table.sample_product_id'
        );
        
        $this->getSelect()->join(
            $salesOrderItem.' as soi',
            'main_table.sample_product_id = soi.product_id AND soi.order_id = spo2.order_id',
            [
                'qty_canceled' => 'qty_canceled'
            ]
        );
        $this->getSelect()->where('spo2.customer_id = '.$customerId)
        ->group('spo2.sample_id');
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('sum(soi.qty_canceled) as qty_canceled');
        return $this->getConnection()->fetchOne($idsSelect, $this->_bindParams);
    }
}
