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
namespace Webkul\SampleProduct\ViewModel;

class FormViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $catalogHelper;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Webkul\SampleProduct\Model\SampleProductFactory
     */
    protected $sampleProductFactory;

    /**
     * @var \Webkul\SampleProduct\Helper\Data
     */
    private $sampleProductHelper;

    /**
     * @param \Magento\Catalog\Helper\Output $catalogHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Webkul\SampleProduct\Model\SampleProductFactory $sampleProductFactory
     * @param \Webkul\SampleProduct\Helper\Data $sampleProductHelper
     */
    public function __construct(
        \Magento\Catalog\Helper\Output $catalogHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\SampleProduct\Model\SampleProductFactory $sampleProductFactory,
        \Webkul\SampleProduct\Helper\Data $sampleProductHelper
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->priceHelper = $priceHelper;
        $this->productFactory = $productFactory;
        $this->sampleProductFactory = $sampleProductFactory;
        $this->sampleProductHelper = $sampleProductHelper;
    }

    /**
     * GetSampleProductHelper
     */
    public function getSampleProductHelper()
    {
        return $this->sampleProductHelper;
    }

    /**
     * GetCatalogHelper
     */
    public function getCatalogHelper()
    {
        return $this->catalogHelper;
    }

    /**
     * GetSampleProduct
     *
     * @param int $productId
     */
    public function getSampleProduct($productId)
    {
        $collection = $this->sampleProductFactory->create()
            ->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('status', 1);
        $sampleId = $collection->getSampleProductId($productId);
        return $this->productFactory->create()->load($sampleId);
    }

    /**
     * GetFormattedPrice
     *
     * @param float $price
     */
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
