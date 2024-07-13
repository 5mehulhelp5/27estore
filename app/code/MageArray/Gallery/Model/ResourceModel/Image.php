<?php
namespace MageArray\Gallery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Image
 * @package MageArray\Gallery\Model\ResourceModel
 */
class Image extends AbstractDb
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magearray_gallery_image', 'image_id');
    }
}
