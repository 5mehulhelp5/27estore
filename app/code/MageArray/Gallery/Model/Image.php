<?php
namespace MageArray\Gallery\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Image
 * @package MageArray\Gallery\Model
 */
class Image extends AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(\MageArray\Gallery\Model\ResourceModel\Image::Class);
    }
}
