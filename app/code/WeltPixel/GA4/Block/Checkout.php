<?php
namespace WeltPixel\GA4\Block;

/**
 * Class \WeltPixel\GA4\Block\Checkout
 */
class Checkout extends \WeltPixel\GA4\Block\Core
{
    /**
     * Returns the product details for the purchase gtm event
     * @return array
     */
    public function getProducts() {
        $quote = $this->getQuote();
        $products = [];
        $displayOption = $this->helper->getParentOrChildIdUsage();

        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productIdModel = $product;
            $configurableVariantChildSku = null;

            if ($displayOption == \WeltPixel\GA4\Model\Config\Source\ParentVsChild::CHILD) {
                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildren();
                    foreach ($children as $child) {
                        $productIdModel = $child->getProduct();
                        $configurableVariantChildSku = $child->getData('sku');
                    }
                }
            }

            $productDetail = [];
            $productDetail['item_name'] = $this->helper->getProductName($productIdModel);
            $productDetail['affiliation'] = $this->helper->getAffiliationName();;
            $productDetail['item_id'] = $this->helper->getGtmProductId($productIdModel);
            $productDetail['price'] = floatval(number_format($item->getPriceInclTax() ?? 0, 2, '.', ''));
            if ($this->helper->isBrandEnabled()) {
                $productDetail['item_brand'] = $this->helper->getGtmBrand($product);
            }
            if ($this->helper->isVariantEnabled()) {
                $itemVariant = $this->helper->getItemVariant();
                if ((\WeltPixel\GA4\Model\Config\Source\ItemVariant::CHILD_SKU == $itemVariant) && ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)) {
                    if (!$configurableVariantChildSku) {
                        $children = $item->getChildren();
                        foreach ($children as $child) {
                            $configurableVariantChildSku = $child->getData('sku');
                        }
                    }
                    if ($configurableVariantChildSku) {
                        $productDetail['item_variant'] = $configurableVariantChildSku;
                    }
                } else {
                    $variant = $this->helper->checkVariantForProduct($product);
                    if ($variant) {
                        $productDetail['item_variant'] = $variant;
                    }
                }
            }
            $productCategoryIds = $product->getCategoryIds();
            $categoryName =  $this->helper->getGtmCategoryFromCategoryIds($productCategoryIds);
            $ga4Categories = $this->helper->getGA4CategoriesFromCategoryIds($productCategoryIds);
            $productDetail = array_merge($productDetail, $ga4Categories);
            $productDetail['item_list_name'] = $categoryName;
            $productDetail['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';
            $productDetail['quantity'] = (double)$item->getQty();

            /**  Set the custom dimensions */
            $customDimensions = $this->getProductDimensions($product);
            foreach ($customDimensions as $name => $value) :
                $productDetail[$name] = $value;
            endforeach;

            $products[] = $productDetail;
        }

        return $products;
    }

    /**
     * @return float
     */
    public function getCartTotal()
    {
        $quote = $this->getQuote();
        return $quote->getGrandTotal() ?? 0;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        $quote = $this->getQuote();
        return $quote->getCouponCode() ?? '';
    }
}
