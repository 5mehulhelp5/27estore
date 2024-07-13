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
namespace Webkul\SampleProduct\Helper\Product;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface;
use Magento\InventoryCatalogApi\Model\SourceItemsProcessorInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class Inventory extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var $_scopeConfig
     */
    private $_scopeConfig;
    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var SourceItemsProcessorInterface
     */
    private $sourceItemsProcessor;

    /**
     * @var IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;

    /**
     * @var DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SourceItemRepositoryInterface
     */
    private $sourceItemRepository;

    /**
     * @var GetSourceItemsDataBySku
     */
    private $sourceDataBySku;

    /**
     * @var GetSalableQuantityDataBySku
     */
    private $salableQuantityDataBySku;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param SourceItemsProcessorInterface $sourceItemsProcessor
     * @param IsSingleSourceModeInterface $isSingleSourceMode
     * @param DefaultSourceProviderInterface $defaultSourceProvider
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param GetSourceItemsDataBySku $sourceDataBySku
     * @param GetSalableQuantityDataBySku $salableQuantityDataBySku
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        SourceItemsProcessorInterface $sourceItemsProcessor,
        IsSingleSourceModeInterface $isSingleSourceMode,
        DefaultSourceProviderInterface $defaultSourceProvider,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SourceItemRepositoryInterface $sourceItemRepository,
        GetSourceItemsDataBySku $sourceDataBySku,
        GetSalableQuantityDataBySku $salableQuantityDataBySku
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->sourceItemsProcessor = $sourceItemsProcessor;
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->defaultSourceProvider = $defaultSourceProvider;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceDataBySku = $sourceDataBySku;
        $this->salableQuantityDataBySku = $salableQuantityDataBySku;
        parent::__construct($context);
    }

    /**
     * GetSalableQtyBySku
     *
     * @param string $sku
     *
     * @return string
     */
    public function getSalableQtyBySku($sku)
    {
        if ($sku) {
            $stockData = $this->salableQuantityDataBySku->execute($sku);
            return isset($stockData[0]['qty']) ? $stockData[0]['qty'] : 0;
        }
        return '';
    }

    /**
     * ManageSampleProductStock
     *
     * @param array $sampleRequestData
     * @param array $requestProductData
     *
     * @return array
     */
    public function manageSampleProductStock($sampleRequestData, $requestProductData)
    {
        $sampleRequestData['product']['quantity_and_stock_status'] =
        $requestProductData['product']['quantity_and_stock_status'];
        if (empty($requestProductData['sample']['qty'])) {
            $parentProductStockData = $this->salableQuantityDataBySku->execute($requestProductData['product']['sku']);
            $requestProductData['sample']['qty'] = isset($parentProductStockData[0]) ? $parentProductStockData
            [0]['qty'] : 0;
        }
        $sampleRequestData['product']['quantity_and_stock_status']['qty'] =
        $requestProductData['sample']['qty'];

        if ($requestProductData && !empty($requestProductData['sample']['qty'])) {
            $sampleRequestData['product']['stock_data']['qty'] = $requestProductData['sample']['qty'];
            $stockData = isset($requestProductData['stock_data']) ?
            $requestProductData['stock_data'] : [];
            if (isset($requestData['sample']['qty']) && (double) $requestProductData['sample']['qty'] > 99999999.9999) {
                $sampleRequestData['product']['stock_data']['qty'] = 99999999.9999;
            }
            if (isset($stockData['min_qty']) && (int) $stockData['min_qty'] < 0) {
                $sampleRequestData['product']['stock_data']['min_qty'] = 0;
            }
            if (!isset($stockData['use_config_manage_stock'])) {
                $sampleRequestData['product']['stock_data']['use_config_manage_stock'] = 0;
                $sampleRequestData['product']['stock_data']['manage_stock'] = 1;
            } else {
                if ($stockData['use_config_manage_stock'] == 1 && !isset($stockData['manage_stock'])) {
                    $sampleRequestData['product']['stock_data']['manage_stock'] = $this->stockConfiguration
                    ->getManageStock();
                }
            }
        }
        if (!empty($requestProductData['salable_quantity'])) {
            $sampleRequestData['salable_quantity'] = $requestProductData['salable_quantity'];
            foreach ($sampleRequestData['salable_quantity'] as $key => $value) {
                if (!empty($requestProductData['sample']['qty'])) {
                    $sampleRequestData['salable_quantity'][$key]['qty'] = $requestProductData['sample']['qty'];
                }
            }
        } else {
            $sampleRequestData['salable_quantity'][0]['qty'] = $requestProductData['sample']['qty'];
            $sampleRequestData['salable_quantity'][0]['manage_stock'] = 1;
        }
        return $sampleRequestData;
    }

    /**
     * ProcessSourceItems
     *
     * @param array $sampleRequestData
     * @param array $sampleProduct
     */
    public function processSourceItems($sampleRequestData, $sampleProduct)
    {
        if ($this->isSourceItemManagementAllowedForProductType->execute($sampleProduct->getTypeId()) === false) {
            return;
        }
        $sampleProductSku = $sampleProduct->getSku();
        $sampleProductData = $sampleRequestData['product'];
        $sampleProductQty = $sampleRequestData['salable_quantity'][0]['qty'] ?? 0;
        $singleSourceData = $sampleProductData['quantity_and_stock_status'] ?? [];
        $singleSourceData['qty'] = $sampleProductQty;

        if (!$this->isSingleSourceMode->execute()) {
            $sources = $this->_request->getParam('sources', []);
            if (isset($sources['assigned_sources']) && is_array($sources['assigned_sources'])) {
                $assignedSources = $this->prepareAssignedSources($sources['assigned_sources']);
                foreach ($assignedSources as $key => $value) {
                    $assignedSources[$key]['quantity'] = $sampleProductQty;
                }
            } else {
                $assignedSources = $this->sourceDataBySku->execute($sampleProductSku);
                foreach ($assignedSources as $key => $value) {
                    $assignedSources[$key]['quantity'] = $sampleProductQty;
                }
            }
            $this->sourceItemsProcessor->execute((string)$sampleProductSku, $assignedSources);
        } elseif (!empty($singleSourceData)) {
            /** @var StockItemInterface $stockItem */
            $stockItem = $sampleProduct->getExtensionAttributes()->getStockItem();
            $sampleProductQty = $singleSourceData['qty'] ?? (empty($stockItem) ? 0 : $stockItem->getQty());
            if ((string)(float)$sampleProductQty == $sampleProductQty) {
                $sampleProductQty = $sampleProductQty;
            } else {
                $sampleProductQty = 0;
            }
            $isInStock = $singleSourceData['is_in_stock'] ?? (empty($stockItem) ? 1 : (int)$stockItem->getIsInStock());
            $defaultSourceData = [
                SourceItemInterface::SKU => $sampleProductSku,
                SourceItemInterface::SOURCE_CODE => $this->defaultSourceProvider->getCode(),
                SourceItemInterface::QUANTITY => $sampleProductQty,
                SourceItemInterface::STATUS => $isInStock,
            ];
            $sourceItems = $this->getSourceItemsWithoutDefaultBySku($sampleProductSku);
            $sourceItems[] = $defaultSourceData;
            $this->sourceItemsProcessor->execute((string)$sampleProductSku, $sourceItems);
        }
    }

    /**
     * Get Source Items Data without Default Source by Sample Product SKU
     *
     * @param string $sampleProductSku
     * @return array
     */
    private function getSourceItemsWithoutDefaultBySku(string $sampleProductSku): array
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(
                SourceItemInterface::SKU,
                $sampleProductSku
            )
            ->addFilter(
                SourceItemInterface::SOURCE_CODE,
                $this->defaultSourceProvider->getCode(),
                'neq'
            )
            ->create();
        $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();

        $sourceItemData = [];
        if ($sourceItems) {
            foreach ($sourceItems as $sourceItem) {
                $sourceItemData[] = [
                    SourceItemInterface::SKU => $sourceItem->getSku(),
                    SourceItemInterface::SOURCE_CODE => $sourceItem->getSourceCode(),
                    SourceItemInterface::QUANTITY => $sourceItem->getQuantity(),
                    SourceItemInterface::STATUS => $sourceItem->getStatus(),
                ];
            }
        }
        return $sourceItemData;
    }

    /**
     * Convert built-in UI component property qty into quantity and source_status into status
     *
     * @param array $inventorySourceList
     * @return array
     */
    private function prepareAssignedSources(array $inventorySourceList): array
    {
        foreach ($inventorySourceList as $key => $source) {
            if (!key_exists('quantity', $source) && isset($source['qty'])) {
                $source['quantity'] = (int) $source['qty'];
                $inventorySourceList[$key] = $source;
            }
            if (!key_exists('status', $source) && isset($source['source_status'])) {
                $source['status'] = (int) $source['source_status'];
                $inventorySourceList[$key] = $source;
            }
        }
        return $inventorySourceList;
    }
}
