<?php

namespace WeltPixel\OwlCarouselSlider\Model\CatalogWidget;

use Magento\CatalogWidget\Model\Rule\Condition\Product;

/**
 * Banner Model
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class ConditionCombine extends \Magento\CatalogWidget\Model\Rule\Condition\Combine
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CatalogWidget\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param \Magento\Framework\Registry $registry,
     * @param array $data
     * @param array $excludedAttributes
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CatalogWidget\Model\Rule\Condition\ProductFactory $conditionFactory,
        \Magento\Framework\Registry $registry,
        array $data = [],
        array $excludedAttributes = []
    ) {
        parent::__construct($context, $conditionFactory, $data, $excludedAttributes);
        $this->registry = $registry;
    }
    public function getNewChildSelectOptions()
    {

        $owlCarouselWidgetLink = trim($this->registry->registry('weltpixel_owlcarousel_widget_condition') ?? '');
        if ($owlCarouselWidgetLink != 'allow_quantity') {
            return parent::getNewChildSelectOptions();
        }

        $excludedAttributes = [];

        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            if (!in_array($code, $excludedAttributes)) {
                $attributes[] = [
                    'value' => Product::class . '|' . $code,
                    'label' => $label,
                ];
            }
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Magento\CatalogWidget\Model\Rule\Condition\Combine::class,
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $attributes]
            ]
        );
        return $conditions;
    }
}
