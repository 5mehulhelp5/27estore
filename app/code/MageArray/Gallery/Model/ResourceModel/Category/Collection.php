<?php
namespace MageArray\Gallery\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package MageArray\Gallery\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            \MageArray\Gallery\Model\Category::Class,
            \MageArray\Gallery\Model\ResourceModel\Category::Class
        );
    }
}
