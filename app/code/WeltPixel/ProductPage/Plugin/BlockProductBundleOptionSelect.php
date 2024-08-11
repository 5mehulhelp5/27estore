<?php

namespace WeltPixel\ProductPage\Plugin;

class BlockProductBundleOptionSelect
{
    /**
     *
     * @var  \WeltPixel\ProductPage\Helper\Data
     */
    protected $wpHelper;

    /**
     * @param \WeltPixel\ProductPage\Helper\Data $wpHelper
     */
    public function __construct(
        \WeltPixel\ProductPage\Helper\Data $wpHelper
    ) {
        $this->wpHelper = $wpHelper;
    }

    /**
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Select $subject
     * @param string $result
     * @return string
     */
    public function afterGetTemplate(
        \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Select $subject,
        $result
    )
    {
        $qtyType = $this->wpHelper->getQtyType();
        switch ($qtyType) {
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_SELECT:
                $result = 'WeltPixel_ProductPage::product/view/type/bundle/select/addtocart_select.phtml';
                break;
            case \WeltPixel\ProductPage\Model\Config\Source\QtyType::QTY_PLUS_MINUS:
                $result = 'WeltPixel_ProductPage::product/view/type/bundle/select/addtocart_plus_minus.phtml';
                break;
        }

        return $result;
    }
}
