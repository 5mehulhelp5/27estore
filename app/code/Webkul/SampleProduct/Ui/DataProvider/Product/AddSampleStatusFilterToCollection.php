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

class AddSampleStatusFilterToCollection implements \Magento\Ui\DataProvider\AddFilterToCollectionInterface
{
    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Data\Collection $collection, $field, $condition = null)
    {
        if (isset($condition['eq'])) {
            if ($field == 'sample_status') {
                if ($condition['eq'] == '0') {
                    $collection->getSelect()->where('at_sp.status = 0 OR at_sp.status IS NULL');
                } else {
                    $collection->getSelect()->where('at_sp.status='.$condition['eq']);
                }
            }
        }
    }
}
