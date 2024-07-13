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
namespace Webkul\SampleProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\SampleProduct\Model\SampleProductFactory;
use Webkul\SampleProduct\Helper\Product\Inventory as InventoryHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;

class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $_request;
    /**
     * @var \Webkul\SampleProduct\Model\SampleProductFactory
     */
    private $_sampleProductFactory;

    /**
     * @var InventoryHelper
     */
    private $inventoryHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var InitializationHelper
     */
    private $initializationHelper;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    private $productTypeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $_escaper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $_product;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     *
     * @param RequestInterface $request
     * @param SampleProductFactory $sampleProductFactory
     * @param InventoryHelper $inventoryHelper
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param InitializationHelper $initializationHelper
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        RequestInterface $request,
        SampleProductFactory $sampleProductFactory,
        InventoryHelper $inventoryHelper,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        InitializationHelper $initializationHelper,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_request = $request;
        $this->_sampleProductFactory = $sampleProductFactory;
        $this->inventoryHelper = $inventoryHelper;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->initializationHelper = $initializationHelper;
        $this->productTypeManager = $productTypeManager;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->_escaper = $escaper;
        $this->_product = $product;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Product save after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $allowed = ['simple', 'configurable'];
            $productType = $this->_request->getParam('type');

            $product = $observer->getProduct();
            $requestData = $this->_request->getParams();

            if (in_array($productType, $allowed) == false || !isset($requestData['sample'])) {
                return;
            }
            
            // set sample request data
            $sampleProductId = '';
            $collection = $this->_sampleProductFactory->create()
                ->getCollection()
                ->addFieldToFilter('product_id', $product->getId());
            foreach ($collection as $value) {
                $sampleProductId = $value->getSampleProductId();
            }
            if (!$sampleProductId && !$requestData['sample']['status']) {
                return;
            }
            $sampleRequestData = $this->createSampleProductRequestData(
                $sampleProductId,
                $requestData,
                $product->getId()
            );
            $this->saveSampleProduct(
                $product->getId(),
                $requestData['sample']['status'],
                $sampleRequestData
            );
            if (!empty($requestData['use_default'])) {
                $requestData['use_default']['name'] = 1;
                $this->_request->setPostValue('use_default', $requestData['use_default']);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * CreateSampleProductRequestData
     *
     * @param int $sampleProductId
     * @param array $requestData
     * @param int $product_id
     */
    public function createSampleProductRequestData($sampleProductId, $requestData, $product_id)
    {
        
        $sampleRequestData = [];
        $sampleRequestData['id'] = $sampleProductId;
        $sampleRequestData['store'] = $this->_request->getParam('store', 0);
        $sampleRequestData['set'] = (int) $this->_request->getParam('set');
        $sampleRequestData['type'] = 'sample';
        $sampleRequestData['product'] = [];
        if (!empty($requestData['use_default'])) {
            $requestData['use_default']['name'] = 0;
            $this->_request->setPostValue('use_default', $requestData['use_default']);
        }
        if ($requestData['sample']['status']) {
            $sampleRequestData['product']['status'] = Product\Attribute\Source\Status::STATUS_ENABLED;
        } else {
            $sampleRequestData['product']['status'] = Product\Attribute\Source\Status::STATUS_DISABLED;
        }

        if ($sampleProductId && !$requestData['sample']['status']) {
            
                $storeId = $this->_request->getParam('store', 0);
                $product = $this->productRepository->getById($sampleProductId, true, $storeId);
                $sampleRequestData['product']['product'] = $product['name'];
                $sampleRequestData['product']['price'] = $product['price'];
                $sampleRequestData['product']['sku'] = $product['sku'];
                $sampleRequestData['product']['description'] = $product['description'];
                $sampleRequestData['product']['quantity_and_stock_status'] = $product['quantity_and_stock_status'];
                $sampleRequestData['product']['visibility'] = $product['visibility'];
                $sampleRequestData['product']['product_has_weight'] = $product['product_has_weight'];
                $sampleRequestData['product']['weight'] = $product['weight'];
                $sampleRequestData['product']['type_id'] = 'sample';
        }
        if ($requestData['sample']['status']) {
            if (empty($requestData['sample']['title'])) {
                $sampleRequestData['product']['name'] = $requestData['product']['name'].' - Sample';
            } else {
                $sampleRequestData['product']['name'] = $this->_escaper
                ->escapeHtml(strip_tags($requestData['sample']['title']));
            }
            if (empty($requestData['sample']['price'])) {
                $sampleRequestData['product']['price'] = '0.0000';
            } else {
                $sampleRequestData['product']['price'] = $this->_escaper->escapeHtml($requestData['sample']['price']);
            }
        }
        
        $sampleRequestData['product']['image'] = $requestData['product']['image']??'';
       
        $sampleRequestData['product']['sku'] = $requestData['product']['sku'].'-sample';
        $sampleRequestData['product']['description'] = $requestData['product']['description'];
        $sampleRequestData['product']['visibility'] = Product\Visibility::VISIBILITY_NOT_VISIBLE;
        $sampleRequestData['product']['product_has_weight'] = $requestData['product']['product_has_weight'];
        $sampleRequestData['product']['weight'] = $requestData['product']['weight'];
        $sampleRequestData['product']['website_ids'] = $requestData['product']['website_ids'];
        $sampleRequestData['product']['type_id'] = 'sample';
        /*
        * Manage sample product Stock data
        */
        $sampleRequestData = $this->inventoryHelper->manageSampleProductStock($sampleRequestData, $requestData);

        return $sampleRequestData;
    }

    /**
     * SaveSampleProduct
     *
     * @param int $productId
     * @param bool $sampleStatus
     * @param array $sampleRequestData
     */
    public function saveSampleProduct($productId, $sampleStatus, $sampleRequestData)
    {
        if ($sampleRequestData) {
            try {
                $productData = $sampleRequestData['product'];
                $sampleProduct = $this->initializationHelper->initializeFromData(
                    $this->sampleProductBuild($sampleRequestData),
                    $productData
                );
                $this->productTypeManager->processProduct($sampleProduct);
                $sampleProduct->setTypeId('sample');
                if (isset($sampleRequestData['product'][$sampleProduct->getIdFieldName()])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The sample product was unable to be saved. Please try again.')
                    );
                }
      
                $qty = $sampleRequestData['salable_quantity'][0]['qty'] ?? 0;
                $originalSku = $sampleProduct->getSku();
                $canSaveCustomOptions = $sampleProduct->getCanSaveCustomOptions();
                $sampleProduct->save();
                $this->inventoryHelper->processSourceItems($sampleRequestData, $sampleProduct);
                $sampleProduct->setStockData(['qty' => $qty, 'is_in_stock' => 1]);
                $sampleProduct->setQuantityAndStockStatus(['qty' => $qty, 'is_in_stock' => 1]);
                $sampleProduct->save();

                $mediaPath =  $this->filesystem
                                   ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                                   ->getAbsolutePath();
                $importDir = $mediaPath.'catalog/product';
                if ($sampleProduct->getImage()) {
                    $img_url = $importDir.$sampleProduct->getImage()??'';
                    $result1 = rtrim($img_url, ".temp");
                    $sampleProduct->addImageToMediaGallery($result1, ['image', 'small_image', 'thumbnail']);
                    $sampleProduct->save();
                }
                $sampleProductId = $sampleProduct->getEntityId();
                $sampleId = '';
                $collection = $this->_sampleProductFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('sample_product_id', $sampleProductId);
                foreach ($collection as $value) {
                    $sampleId = $value->getId();
                }
                // save sample data to custom table
                $sample = $this->_sampleProductFactory->create();
                if ($sampleId) {
                    $sample->setId($sampleId);
                }
                $sample->setProductId($productId);
                $sample->setSampleProductId($sampleProductId);
                $sample->setStatus($sampleStatus);
                $sample->save();

                $extendedData = $sampleRequestData;
                $extendedData['can_save_custom_options'] = $canSaveCustomOptions;
                $this->copySampleDataToStores($extendedData, $sampleProductId);
                $this->messageManager->addSuccessMessage(__('You saved the sample for this product.'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }

    /**
     * SampleProductBuild
     *
     * @param array $sampleRequestData
     */
    public function sampleProductBuild($sampleRequestData): ProductInterface
    {
        $productId = (int) $sampleRequestData['id'];
        $storeId = $sampleRequestData['store'];
        $attributeSetId = (int) $sampleRequestData['set'];
        $typeId = $sampleRequestData['type'];

        if ($productId) {
            try {
                $product = $this->productRepository->getById($productId, true, $storeId);
                if ($attributeSetId) {
                    $product->setAttributeSetId($attributeSetId);
                }
            } catch (\Exception $e) {
                $product = $this->createEmptySampleProduct(
                    \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE,
                    $attributeSetId,
                    $storeId
                );
                $this->logger->critical($e);
            }
        } else {
            $product = $this->createEmptySampleProduct($typeId, $attributeSetId, $storeId);
        }

        return $product;
    }

    /**
     * Create a product with the given properties
     *
     * @param int $typeId
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product
     */
    private function createEmptySampleProduct($typeId, $attributeSetId, $storeId): Product
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->productFactory->create();
        $product->setData('_edit_mode', true);

        if ($typeId !== null) {
            $product->setTypeId($typeId);
        }

        if ($storeId !== null) {
            $product->setStoreId($storeId);
        }

        if ($attributeSetId) {
            $product->setAttributeSetId($attributeSetId);
        }

        return $product;
    }

    /**
     * Do copying data to stores
     *
     * @param array $sampleData
     * @param int $sampleProductId
     */
    protected function copySampleDataToStores($sampleData, $sampleProductId)
    {
        if (!empty($sampleData['product']['copy_to_stores'])) {
            foreach ($sampleData['product']['copy_to_stores'] as $websiteId => $group) {
                if (isset($sampleData['product']['website_ids'][$websiteId])
                    && (bool)$sampleData['product']['website_ids'][$websiteId]) {
                    foreach ($group as $store) {
                        $this->copySampleDataToStore($sampleData, $sampleProductId, $store);
                    }
                }
            }
        }
    }

    /**
     * Do copying sample data to stores
     *
     * If the 'copy_from' field is not specified in the input data,
     * the store fallback mechanism will automatically take the admin store's default value.
     *
     * @param array $sampleData
     * @param int $sampleProductId
     * @param array $store
     */
    private function copySampleDataToStore($sampleData, $sampleProductId, $store)
    {
        if (isset($store['copy_from'])) {
            $copyFrom = $store['copy_from'];
            $copyTo = (isset($store['copy_to'])) ? $store['copy_to'] : 0;
            if ($copyTo) {
                $this->_product
                    ->setStoreId($copyFrom)
                    ->load($sampleProductId)
                    ->setStoreId($copyTo)
                    ->setCanSaveCustomOptions($sampleData['can_save_custom_options'])
                    ->setCopyFromView(true)
                    ->save();
            }
        }
    }
}
