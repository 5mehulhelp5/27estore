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
namespace Webkul\SampleProduct\Plugin\InventorySales\Model\IsProductSalableForRequestedQtyCondition;

use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterface;
use Magento\InventorySalesApi\Api\Data\ProductSalabilityErrorInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\ProductSalableResultInterfaceFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\Phrase;
use Webkul\SampleProduct\Helper\Data as HelperData;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;

class IsCorrectQtyCondition
{
    /**
     * @var ProductSalabilityErrorInterfaceFactory
     */
    protected $productSalabilityErrorFactory;

    /**
     * @var ProductSalableResultInterfaceFactory
     */
    protected $productSalableResultFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var GetReservationsQuantityInterface
     */
    private $getReservationsQuantity;

    /**
     * @var GetStockItemDataInterface
     */
    private $getStockItemData;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @param ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory
     * @param ProductSalableResultInterfaceFactory $productSalableResultFactory
     * @param ProductRepositoryInterface $productRepository
     * @param HelperData $helperData
     * @param GetReservationsQuantityInterface $getReservationsQuantity
     * @param GetStockItemDataInterface $getStockItemData
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param StockItemRepository $stockItemRepository
     */
    public function __construct(
        ProductSalabilityErrorInterfaceFactory $productSalabilityErrorFactory,
        ProductSalableResultInterfaceFactory $productSalableResultFactory,
        ProductRepositoryInterface $productRepository,
        HelperData $helperData,
        GetReservationsQuantityInterface $getReservationsQuantity,
        GetStockItemDataInterface $getStockItemData,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        StockItemRepository $stockItemRepository
    ) {
        $this->productSalabilityErrorFactory = $productSalabilityErrorFactory;
        $this->productSalableResultFactory = $productSalableResultFactory;
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        $this->stockItemRepository = $stockItemRepository;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->getStockItemData = $getStockItemData;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function aroundExecute(
        \Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\IsCorrectQtyCondition $subject,
        callable $proceed,
        string $sku,
        int $stockId,
        float $requestedQty
    ) {
        if ($this->helperData->isSampleProductEnable()) {
            $product = $this->productRepository->get($sku);
            $productId = $product->getId();
            if ($this->helperData->isCurrentSampleProduct($productId)) {
                $allowProductLimit = $this->helperData->maxSampleQty();
                $sku = $product->getSku();
                $stockItemData = $this->getStockItemData->execute($sku, $stockId);
                if (null === $stockItemData) {
                    return $this->createErrorResult(
                        'is_salable_with_reservations-no_data',
                        __('The requested sku is not assigned to given stock')
                    );
                }

                /** @var StockItemConfigurationInterface $stockItemConfiguration */
                $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, $stockId);

                $qtyWithReservation = $stockItemData[GetStockItemDataInterface::QUANTITY] +
                    $this->getReservationsQuantity->execute($sku, $stockId);
                $qtyLeftInStock = $qtyWithReservation - $stockItemConfiguration->getMinQty();
                $isInStock = bccomp((string)$qtyLeftInStock, (string)$requestedQty, 4) >= 0;
                $isEnoughQty = (bool)$stockItemData[GetStockItemDataInterface::IS_SALABLE] && $isInStock;

                if (!$isEnoughQty) {
                    return $this->createErrorResult(
                        'is_correct_qty-max_sale_qty',
                        __('No quantity available for sample "%1"', $product->getName())
                    );
                } elseif ($allowProductLimit && $allowProductLimit != "" && $this->helperData->allowToLoginCustomer()) {
                    if ($allowProductLimit < $requestedQty) {
                        return $this->createErrorResult(
                            'is_correct_qty-max_sale_qty',
                            __(
                                'The requested qty exceeds the maximum %1 qty allowed in shopping cart',
                                $allowProductLimit
                            )
                        );
                    } elseif (!empty($this->helperData->getCustomerId())) {
                        $customerOrderedSampleQty = $this->helperData->getCustomerOrderedSampleQty($productId);
                        if ($customerOrderedSampleQty >= $allowProductLimit) {
                            return $this->createErrorResult(
                                'is_correct_qty-max_sale_qty',
                                __(
                                    'You have already purchased the allowed sample qty(s) for "%1"',
                                    $product->getName()
                                )
                            );
                        }
                    }
                }
            }
        }
        return $proceed($sku, $stockId, $requestedQty);
    }

    /**
     * Create Error Result Object
     *
     * @param string $code
     * @param Phrase $message
     * @return ProductSalableResultInterface
     */
    public function createErrorResult(string $code, Phrase $message): ProductSalableResultInterface
    {
        $errors = [
            $this->productSalabilityErrorFactory->create([
                'code' => $code,
                'message' => $message
            ])
        ];
        return $this->productSalableResultFactory->create(['errors' => $errors]);
    }
}
