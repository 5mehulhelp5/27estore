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
namespace Webkul\SampleProduct\Plugin\Model\Indexer\Product\Price\Action;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Ddl\Table;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Row
{
    public const CATALOG_PRODUCT_TABLE = 'catalog_product_index_price';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Constructor Initialize
     *
     * @param ResourceConnection $resourceConnection
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ProductRepositoryInterface $productRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
    }
    /**
     * Before Execute
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Action\Row $subject
     * @param int $id
     * @return int
     */
    public function beforeExecute(
        \Magento\Catalog\Model\Indexer\Product\Price\Action\Row $subject,
        $id
    ) {
        $connection  = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(self :: CATALOG_PRODUCT_TABLE);
        $columnName = 'id';
        $productType = $this->productRepository->getById($id)->getTypeId();
        if ($productType == 'sample') {
            if ($connection->tableColumnExists($tableName, $columnName) === false) {
                $connection->addColumn($tableName, $columnName, [
                    'identity'  => false,
                    'type' => Table::TYPE_SMALLINT,
                    'nullable'  => false,
                    'comment'   => 'Product Id',
                    'unsigned'  => true,
                ]);
            }
        }
        return $id;
    }
}
