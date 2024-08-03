<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\GoogleShoppingFeed\Model\XmlFeed;

use Magefan\GoogleShoppingFeed\Model\Config;
use Magefan\GoogleShoppingFeed\Setup\Patch\Data\AddMfGoogleProductAttribute;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Gallery\Processor;
use Magento\Framework\UrlInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magefan\Community\Api\GetCategoryByProductInterface;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

class ProductData
{
    //https://support.google.com/merchants/answer/6324406?hl=en
    //Repeated field
    const LIMIT_FOR_NUMBER_OF_ATTRIBUTE = 5;

    const ANALYTICS_SETUP_FIELDS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Processor
     */
    private $imageProcessor;

    /**
     * @var ConfigurableProduct
     */
    private $configurableProduct;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var array
     */
    private $categoriesProduct;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var GetCategoryByProductInterface
     */
    private $getCategoryByProduct;

    /**
     * @var Tree
     */
    private $categoryTree;

    /**
     * @var GalleryReadHandler
     */
    private $galleryReadHandler;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Config $config
     * @param Processor $imageProcessor
     * @param ConfigurableProduct $configurableProduct
     * @param StockRegistryInterface $stockRegistry
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param GetCategoryByProductInterface $getCategoryByProduct
     * @param Tree $categoryTree
     * @param GalleryReadHandler $galleryReadHandler
     */
    public function __construct(
        StoreManagerInterface       $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        Config                      $config,
        Processor                   $imageProcessor,
        ConfigurableProduct         $configurableProduct,
        StockRegistryInterface      $stockRegistry,
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        GetCategoryByProductInterface $getCategoryByProduct,
        Tree                        $categoryTree,
        GalleryReadHandler           $galleryReadHandler
    )
    {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->config = $config;
        $this->imageProcessor = $imageProcessor;
        $this->configurableProduct = $configurableProduct;
        $this->stockRegistry = $stockRegistry;
        $this->registry = $registry;
        $this->priceCurrency = $priceCurrency;
        $this->getCategoryByProduct = $getCategoryByProduct;
        $this->categoryTree = $categoryTree;
        $this->galleryReadHandler = $galleryReadHandler;
    }

    /**
     * @param $product
     * @param $attribute
     * @return string
     * @throws NoSuchEntityException
     */
    public function getData($product, $attribute)
    {
        $method = $this->getFuncName((string)$attribute);
        if (method_exists($this, $method)) {
            $value = $this->$method($product);
        } elseif (method_exists($product, $method)) {
            $value = $product->{$method}();
        } else {
            $value = $this->getAttribute($product, $attribute);
        }

        if (in_array($attribute, $this->imageProcessor->getMediaAttributeCodes())) {
            if ((!$value || ($value == 'no_selection')) && ($parentProduct = $product->getMfParentProduct())) {
                $value = $this->getData($parentProduct, $attribute);
            } else {
                $mediaCatalogProductUrl = $this->getMediaCatalogProductUrl();
                $mediaUrlSlash =   (strcmp(mb_substr($mediaCatalogProductUrl, -1), "/") === 0) ? "" : "/";
                $value =  $mediaCatalogProductUrl . $mediaUrlSlash . $value;
            }
        }

        if (is_string($value) && false !== strpos($value, '#html-body')) {
            $domDocument = new \DOMDocument('1.0', 'UTF-8');
            $domDocument->loadHTML($value);
            $value = preg_replace('/#html-body[^{]*{[^}]*}/', '', $domDocument->textContent);
        }

        if (is_string($value)) {
            /* Fix issue related with non-printed symbol like <0xa0> */
            $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
        }

        return $value ?: '';
    }

    /**
     * @param string $value
     * @return string
     */
    private function getFuncName(string $value): string
    {
        return "get" . str_replace(' ', '', ucwords(strtolower(str_replace("_", " ", $value))));
    }

    /**
     * @param $product
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCategoryIds($product)
    {
        $values = [];
        if ($productCategories = $this->getCategoriesByProduct($product)) {
            foreach ($productCategories as $productCategory) {
                if (self::LIMIT_FOR_NUMBER_OF_ATTRIBUTE <= count($values)) {
                    break;
                }

                $categoryIds = $productCategory->getPathIds();
                foreach ($categoryIds as $categoryId) {
                    $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());

                    if ($category->getLevel() < 2) {
                        continue;
                    }

                    $values[$productCategory->getId()][] = $category->getName();
                }
            }
        }

        return $values;
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getGoogleProductCategory($product): string
    {
        if ($googleIdProduct = $this->getGoogleIdProduct($product)) {
            return $googleIdProduct;
        }

        $parentProduct = $product->getMfParentProduct();
        if ($parentProduct && ($googleIdProduct = $this->getGoogleIdProduct($parentProduct))) {
            return $googleIdProduct;
        }

        if ($productCategory = $this->getCategoryByProduct($product)) {
            return $this->getGoogleIdCategory($productCategory) ?: '';
        }

        return '';
    }

    /**
     * @param $product
     * @return false|\Magento\Catalog\Api\Data\CategoryInterface|mixed
     * @throws NoSuchEntityException
     */
    private function getCategoriesByProduct($product)
    {
        if (!isset($this->categoriesProduct[$product->getId()])) {
            $productCategories = [];

            $categoryIds = $product->getCategoryIds();

            $parentProduct = $product->getMfParentProduct();
            if ($parentProduct) {
                $categoryIds = array_unique(array_merge($categoryIds, $parentProduct->getCategoryIds()));
            }

            if ($categoryIds) {
                $store = $this->storeManager->getStore();
                $rootCategoryId = $store->getRootCategoryId();

                $firstLevelCategoryChildIds = [];

                foreach ($categoryIds as $categoryId) {
                    try {
                        $category = $this->categoryRepository->get($categoryId, $store->getId());

                        //Only include one instance of sub category from first level category
                        if ((3 == $category->getLevel()) && isset($firstLevelCategoryChildIds[$category->getParentId()])) {
                            continue;
                        }

                        //3 - cause' we count from second level,not from first(root)
                        //since first(root) level is not visible for user(on store front)
                        //so not include second level (for user it is first level) categories
                        if ($category->getIsActive()
                            && in_array($rootCategoryId, $category->getPathIds())
                            && (3 <= $category->getLevel())
                        ) {
                            $productCategories[] = $category;

                            if (3 == $category->getLevel()) {
                                $firstLevelCategoryChildIds[$category->getParentId()][] = $categoryId;
                            }
                        }
                    } catch (\Exception $e) {
                        /* Do nothing */
                    }
                }

            }

            $this->categoriesProduct[$product->getId()] = $productCategories;
        }

        return $this->categoriesProduct[$product->getId()];
    }

    /**
     * @param $product
     * @return false|\Magento\Catalog\Api\Data\CategoryInterface|mixed
     * @throws NoSuchEntityException
     */
    private function getCategoryByProduct($product)
    {
        return $this->getCategoryByProduct->execute($product);
    }

    /**
     * Google Category Id
     * from Product Settings
     *
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    private function getGoogleIdProduct($product): string
    {
        $productAttribute = $product->getData(AddMfGoogleProductAttribute::MF_GOOGLE_PRODUCT_PRODUCT);
        if (null === $productAttribute) {
            $productAttribute = $product->getResource()->getAttributeRawValue(
                $product->getId(),
                AddMfGoogleProductAttribute::MF_GOOGLE_PRODUCT_PRODUCT,
                $this->storeManager->getStore()->getId()
            );
        }
        if (empty($productAttribute) || $productAttribute == '0') {
            return '';
        }

        return $productAttribute;
    }

    /**
     * Google Category Id
     * from Category Settings
     *
     * @param array $categoryIds
     * @param $store
     * @return false|mixed
     * @throws NoSuchEntityException
     */
    private function getGoogleIdCategory($category)
    {
        $categoryIds = $category->getPathIds();
        if (empty($categoryIds) || !is_array($categoryIds)) {
            return false;
        }

        foreach (array_reverse($categoryIds, true) as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                $googleAttribute = $category->getCustomAttribute(
                    AddMfGoogleProductAttribute::MF_GOOGLE_PRODUCT_CATEGORY
                );

                if ($googleAttribute && $googleAttribute->getValue() !== '0') {
                    return $googleAttribute->getValue();
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return false;
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPrice($product): string
    {
        $priceFloat = (float)$product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();

        if (!$priceFloat) {
            return '';
        }

        $currentCurrency = $this->getCurrentCurrencySymbol();
        $price = $this->priceCurrency->convert($priceFloat, $this->storeManager->getStore(), $currentCurrency);

        return number_format($price, 2, '.', '') . ' ' . $currentCurrency;
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSpecialPrice($product): string
    {
        $specialPriceFloat = (float)$product->getData('special_price');
        if (!$specialPriceFloat) {
            return '';
        }

        $currentCurrency = $this->getCurrentCurrencySymbol();
        $price = $this->priceCurrency->convert($specialPriceFloat, $this->storeManager->getStore(), $currentCurrency);

        return number_format($price, 2, '.', '') . ' ' . $currentCurrency;
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFinalPrice($product): string
    {
        $finalPriceFloat = (float)$product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $finalPriceCmp = number_format($finalPriceFloat, 1, '.', '');

        $priceFloat = (float)$product->getPrice();
        if (!$priceFloat) {
            $priceFloat = (float)$product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        }

        $priceCmp = number_format($priceFloat, 1, '.', '');

        if (!$finalPriceFloat || $priceCmp == $finalPriceCmp) {
            return '';
        }

        $currentCurrency = $this->getCurrentCurrencySymbol();
        $price = $this->priceCurrency->convert($finalPriceFloat, $this->storeManager->getStore(), $currentCurrency);

        return number_format($price, 2, '.', '') . ' ' . $currentCurrency;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getCurrentCurrencySymbol(): string
    {
        return $this->registry->registry('mf_current_currency');
    }

    /**
     * @param $product
     * @return string
     */
    public function getProductUrl($product): string
    {
        $options = [];

        $parentProduct = $product->getMfParentProduct();
        if ($parentProduct) {
            $productUrl = $parentProduct->getProductUrl();
            $options['mfpreselect'] = $parentProduct->getId();

            $attributes = $this->configurableProduct->getConfigurableAttributesAsArray($parentProduct);
            foreach ($attributes as $attribute) {
                $id = $attribute['attribute_id'];
                $value = $product->getData($attribute['attribute_code']);
                $options[$id] = $value;
                //for hyva
                $options[$attribute['attribute_code']] = $value;
            }
        } else {
            $productUrl = $product->getProductUrl();
        }

        foreach (self::ANALYTICS_SETUP_FIELDS as $field) {
            $param = $this->config->getGoogleAnalytics($field);
            if (!trim($param)) {
                continue;
            }
            $options[$field] = $param;
        }

        if ($options) {
            $productUrl .= (false === strpos($productUrl, '?')) ? '?' : '&';
            $productUrl .= http_build_query($options);
        }

        return $productUrl;
    }

    /**
     * @param $product
     * @param $attribute
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttribute($product, $attribute): string
    {
        $result = '';

        if ($value = $product->getData($attribute)) {
            if (!is_array($value)) {
                $attributeText = $product->getAttributeText($attribute);
                $result = $attributeText ? $attributeText : $value;

                //When attribute is multiselect (e.g: material)
                if(is_array($result)) {
                    $result = implode(' > ', $result);
                }

                $result = (string)$result;
            }
        }

        $parentProduct = $product->getMfParentProduct();
        if (!$result && $parentProduct) {
            $result = $this->getAttribute($parentProduct, $attribute);
        }

        return $result;
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    private function getProductType($product) {

        $categoryTreeString = '';
        $category = $this->getCategoryByProduct($product);
        if (!$category || !$category->getId()) {
            return $categoryTreeString;
        }

        $categoryTree = $this->categoryTree->setStoreId($product->getStoreId())->loadBreadcrumbsArray($category->getPath());
        foreach ($categoryTree as $item) {
          if ($item['level'] == '1'){
              continue;
          }

            $categoryTreeString .= (strlen($categoryTreeString) ? ' -> ' : '') . $item['name'];
        }

        return $categoryTreeString;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getMediaCatalogProductUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, true)
            . 'catalog/product';
    }

    /**
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getQuantityAndStockStatus($product): string
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        if (!$stockItem) {
            return 'out_of_stock';
        }
        return $stockItem->getIsInStock() ? 'in_stock' : 'out_of_stock';
    }

    /**
     * @param $product
     * @return array
     */
    private function getAllImages($product): array
    {
        $value = [];
        $product = $this->galleryReadHandler->execute($product);
        $dbFieldData = $this->config->getMapField('image_link');
        $productImage = (string)(isset($dbFieldData[0]['attr']) ? $product->{'get' . $dbFieldData[0]['attr']}() : '');

        foreach ($product->getMediaGalleryImages() as $image) {
            if (isset($value[$image->getUrl()]) || (false != strpos($image->getUrl(), $productImage))) {
                continue;
            }

            $value[$image->getUrl()] = $image->getUrl();
        }

        return $value;
    }

    /**
     * @param $product
     * @return string
     */
    public function getDynamicGtin($product): string
    {
        $id = $product->getId();
        $gtin = $id;
        $i = 0;
        $prefix = '';
        while (strlen($gtin) + strlen($prefix) < 11) {
            $i++;
            $prefix .= ($i < 10) ? $i : 0;
        }

        $gtin = $prefix . $gtin;
        $s1 = $s2 = 0;
        for ($i = 0; $i < strlen($gtin); $i++) {
            if ($i % 2) {
                $s2 .= $gtin[$i];
            } else {
                $s1 .= $gtin[$i];
            }
        }

        $s = $s1 * 3 + $s2;
        $l = 10 - ($s % 10);
        if ($l == 10) {
            $l = 0;
        }

        $gtin .= $l;

        return $gtin;
    }

}
