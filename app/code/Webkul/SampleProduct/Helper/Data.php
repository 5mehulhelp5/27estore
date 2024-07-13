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
namespace Webkul\SampleProduct\Helper;

use Webkul\SampleProduct\Model\SampleProductFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Webkul\SampleProduct\Model\ResourceModel\SampleProductOrder\CollectionFactory as SampleProductOrderCollection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Webkul\SampleProduct\Model\SampleProductFactory
     */
    protected $_sampleProductFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SampleProductOrderCollection
     */
    private $_sampleProductOrder;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param SampleProductFactory $sampleProductFactory
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface|null $productRepository
     * @param SampleProductOrderCollection $sampleProductOrder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SampleProductFactory $sampleProductFactory,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository = null,
        SampleProductOrderCollection $sampleProductOrder
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_sampleProductFactory = $sampleProductFactory;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->_sampleProductOrder = $sampleProductOrder;
        parent::__construct($context);
    }

    /**
     * GtCustomerId
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * GetCustomerSampleOrder
     *
     * @param int $productId
     */
    public function getCustomerSampleOrder($productId)
    {
        $customerId = $this->getCustomerId();
        $collection = $this->_sampleProductOrder->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('sample_id', $productId);
        return $collection->getSize();
    }

    /**
     * GetCustomerOrderedSampleQty
     *
     * @param int $sampleProductId
     */
    public function getCustomerOrderedSampleQty($sampleProductId)
    {
        $customerId = $this->getCustomerId();
        $collection = $this->_sampleProductFactory->create()
            ->getCollection()
            ->addFieldToSelect('sample_product_id')
            ->addFieldToFilter('sample_product_id', $sampleProductId);
        $orderedQty = $collection->getSampleProductOrderedQty($customerId);
        $collection2 = $this->_sampleProductFactory->create()
            ->getCollection()
            ->addFieldToSelect('sample_product_id')
            ->addFieldToFilter('sample_product_id', $sampleProductId);
        $canceledQty = $collection2->getSampleProductOrderedCanceledQty($customerId);
        return $orderedQty-$canceledQty;
    }

    /**
     * IsSampleProductEnable
     */
    public function isSampleProductEnable()
    {
        return $this->scopeConfig->getValue(
            'sampleproducts/settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * AllowToLoginCustomer
     */
    public function allowToLoginCustomer()
    {
        return $this->scopeConfig->getValue(
            'sampleproducts/settings/allow_to_login_customer',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * AllowCustomerGroups
     *
     * @param string $storeId
     */
    public function allowCustomerGroups($storeId = null)
    {
        $allowedCustomerGroups = null;
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getCode();
        }
        try {
            $allowedCustomerGroups = $this->scopeConfig->getValue(
                'sampleproducts/settings/allow_customer_groups',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            return $allowedCustomerGroups??'';
        } catch (\Magento\Framework\Exception\State\InitException $e) {
            throw NoSuchEntityException::singleField('storeId', $storeId);
        } catch (NoSuchEntityException $e) {
            throw NoSuchEntityException::singleField('storeId', $storeId);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCustomGroupById($groupId)
    {
        try {
            return $this->groupRepository->getById($groupId);
        } catch (NoSuchEntityException $e) {
            throw NoSuchEntityException::singleField('groupId', $groupId);
        }
    }

    /**
     * MaxSampleQty
     */
    public function maxSampleQty()
    {
        return $this->scopeConfig->getValue(
            'sampleproducts/settings/max_sample_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * IsCurrentSampleProduct
     *
     * @param int $productId
     */
    public function isCurrentSampleProduct($productId)
    {
        $collection = $this->_sampleProductFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('sample_product_id', $productId);
        return $collection->getSize();
    }

    /**
     * GetSampleProductIdByProductId
     *
     * @param int $productId
     */
    public function getSampleProductIdByProductId($productId)
    {
        $sampleProductId = '';
        $collection = $this->_sampleProductFactory->create()
            ->getCollection()
            ->addFieldToFilter('product_id', $productId);
        foreach ($collection as $value) {
            $sampleProductId = $value->getSampleProductId();
        }
        return $sampleProductId;
    }

    /**
     * GetSampleParentProductId
     *
     * @param int $sampleProductId
     */
    public function getSampleParentProductId($sampleProductId)
    {
        $productId = '';
        $collection = $this->_sampleProductFactory->create()
            ->getCollection()
            ->addFieldToFilter('sample_product_id', $sampleProductId);
        foreach ($collection as $value) {
            $productId = $value->getProductId();
        }
        return $productId;
    }

    /**
     * GetSampleProductByProductId
     *
     * @param int $productId
     */
    public function getSampleProductByProductId($productId)
    {
        $sampleProductId = $this->getSampleProductIdByProductId($productId);
        return $this->productFactory->create()->load($sampleProductId);
    }

    /**
     * GetSampleParentProduct
     *
     * @param int $sampleProductId
     */
    public function getSampleParentProduct($sampleProductId)
    {
        $productId = $this->getSampleParentProductId($sampleProductId);
        return $this->productFactory->create()->load($productId);
    }

    /**
     * GetAllSampleProducts
     */
    public function getAllSampleProducts()
    {
        return $collection = $this->_sampleProductFactory->create()
            ->getCollection();
    }
}
