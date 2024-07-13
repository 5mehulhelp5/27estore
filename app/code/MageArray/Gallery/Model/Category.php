<?php
namespace MageArray\Gallery\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Category
 * @package MageArray\Gallery\Model
 */
class Category extends AbstractModel
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(\MageArray\Gallery\Model\ResourceModel\Category::Class);
    }

    /**
     * @param $urlKey
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkUrlKey($urlKey)
    {
        return $this->_getResource()->checkUrlKey($urlKey);
    }
}
