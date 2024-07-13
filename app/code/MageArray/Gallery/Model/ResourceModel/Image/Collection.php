<?php
namespace MageArray\Gallery\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package MageArray\Gallery\Model\ResourceModel\Image
 */
class Collection extends AbstractCollection
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            \MageArray\Gallery\Model\Image::Class,
            \MageArray\Gallery\Model\ResourceModel\Image::Class
        );
    }
}
