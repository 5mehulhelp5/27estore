<?php
namespace WeltPixel\ProductLabels\Plugin\Indexer\StockItem\Save;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Registry;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use WeltPixel\ProductLabels\Model\Indexer\Product\ProductRuleIndexer;

class ApplyMSIRules
{
    /**
     * @var ProductRuleIndexer
     */
    protected $productRuleIndexer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;


    /**
     * @param ProductRuleIndexer $productRuleIndexer
     * @param Registry $registry
     * @param ResourceConnection $resource
     */
    public function __construct(
        ProductRuleIndexer $productRuleIndexer,
        Registry $registry,
        ResourceConnection $resource
    ) {
        $this->productRuleIndexer = $productRuleIndexer;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }


    /**
     * @param \Magento\InventoryCatalog\Plugin\InventoryApi\SetDataToLegacyCatalogInventoryAtSourceItemsSavePlugin $subjectPlugin
     * @param void $resultPlugin
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param void $result
     * @param SourceItemInterface[] $sourceItems
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAfterExecute(
        \Magento\InventoryCatalog\Plugin\InventoryApi\SetDataToLegacyCatalogInventoryAtSourceItemsSavePlugin $subjectPlugin,
        $resultPlugin,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        $result,
        array $sourceItems
    ) {
        $isInStock = false;
        foreach ($sourceItems as $sourceItem) {
            $isInStockStatus = (bool)$sourceItem->getData('status');
            if ($isInStockStatus) {
                $isInStock = true;
            }
        }

        if (count($sourceItems) <= 1) {
            return;
        }

        $this->registry->register('weltpixel_msi_inventory_stock', 'msi_inventory_stock');
        $this->registry->register('weltpixel_msi_inventory_stock_value', $isInStock);

        $firstKey = array_key_first($sourceItems);
        $productId = $this->getProductIdsBySku($sourceItems[$firstKey]->getSku());
        $this->productRuleIndexer->executeRow($productId);
        $this->registry->unregister('weltpixel_msi_inventory_stock');
        $this->registry->unregister('weltpixel_msi_inventory_stock_value');
    }

    /**
     * @param $sku
     * @return int
     */
    protected function getProductIdsBySku($sku)
    {
        $select = $this->connection->select()
            ->from(['product' => $this->resource->getTableName('catalog_product_entity')], ['entity_id'])
            ->where('product.sku = ?', $sku);

        return $this->connection->fetchOne($select);
    }
}
