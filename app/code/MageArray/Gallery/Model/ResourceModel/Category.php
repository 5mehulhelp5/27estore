<?php
namespace MageArray\Gallery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Category
 * @package MageArray\Gallery\Model\ResourceModel
 */
class Category extends AbstractDb
{

    /**
     * @var null
     */
    protected $connection = null;
    protected $_resource;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('magearray_gallery_category', 'category_id');
    }

    /**
     * @param $urlKey
     * @return string
     */
    public function checkUrlKey($urlKey)
    {
        $connection  = $this->_resources->getConnection('core_write');
        $tableName   = $connection->getTableName('magearray_gallery_category');
        $select = $this->getLoadByUrlKeySelect($urlKey, 'enable');
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns($tableName.'.category_id')
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param $urlKey
     * @param null $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function getLoadByUrlKeySelect($urlKey, $isActive = null)
    {
        $connection  = $this->_resources->getConnection('core_write');
        $tableName   = $connection->getTableName('magearray_gallery_category');
        $select = $connection
            ->select()
            ->from($tableName)
            ->where(
                $tableName.'.url_key = ?',
                $urlKey
            );
        if (!empty($isActive)) {
            $select->where('magearray_gallery_category.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    public function getConnection()
    {
        
        if (!$this->connection) {
            
            $connection  = $this->_resources->getConnection('core_write');
            $tableName   = $connection->getTableName('magearray_gallery_category');
        }
       
        return $connection;
    }
}
