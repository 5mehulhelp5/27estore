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
namespace Webkul\SampleProduct\Block\Adminhtml\Product\Edit\Tab;

class Sample extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $_localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Webkul\SampleProduct\Helper\Data
     */
    private $helper;

    /**
     * @var \Webkul\SampleProduct\Helper\Product\Inventory
     */
    private $inventoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param \Webkul\SampleProduct\Helper\Product\Inventory $inventoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\SampleProduct\Helper\Data $helper,
        \Webkul\SampleProduct\Helper\Product\Inventory $inventoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
        $this->_localeCurrency = $localeCurrency;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        $this->inventoryHelper = $inventoryHelper;
    }

    /**
     * IsSampleBlockAllowed
     */
    public function isSampleBlockAllowed()
    {
        $allowed = ['simple', 'configurable'];
        $productType = $this->coreRegistry->registry('product')->getTypeId();
        if (in_array($productType, $allowed) != false) {
            return true;
        }
    }

    /**
     * IsSampleBlockDisable
     */
    public function isSampleBlockDisable()
    {
        $unAllowed = ['downloadable', 'sample'];
        $productType = $this->coreRegistry->registry('product')->getTypeId();
        if (in_array($productType, $unAllowed) != false) {
            return true;
        }
    }

    /**
     * Retrieve currency Symbol.
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_localeCurrency->getCurrency(
            $this->getBaseCurrencyCode()
        )->getSymbol();
    }

    /**
     * GetBaseCurrencyCode
     */
    public function getBaseCurrencyCode()
    {
        return $this->_storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * GetSampleData
     *
     * @return /Webkul/SampleProduct/Model/SampleProduct
     */
    public function getSampleData()
    {
        $productId = $this->coreRegistry->registry('product')->getId();
        return $this->helper->getSampleProductByProductId($productId);
    }

    /**
     * GetSampleProductQty
     *
     * @param string $sku
     * @return string
     */
    public function getSampleProductQty($sku)
    {
        return $this->inventoryHelper->getSalableQtyBySku($sku);
    }
}
