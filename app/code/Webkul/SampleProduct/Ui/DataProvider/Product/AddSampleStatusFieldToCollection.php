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
namespace Webkul\SampleProduct\Ui\DataProvider\Product;

class AddSampleStatusFieldToCollection implements \Magento\Ui\DataProvider\AddFieldToCollectionInterface
{
    /**
     * AddField
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param string $field
     * @param string $alias
     */
    public function addField(\Magento\Framework\Data\Collection $collection, $field, $alias = null)
    {
        $collection->addAttributeToFilter('type_id', ['nin'=>'sample']);
        $collection->joinField(
            'sp',
            'wk_sample_product',
            'at_sp.status as sample_status',
            'product_id=entity_id',
            null,
            'left'
        );
    }
}
