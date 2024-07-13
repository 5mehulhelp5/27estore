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

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Webkul\SampleProduct\Model\ResourceModel\SampleProduct\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class DisableSampleProductConfig implements ObserverInterface
{
    /**
     * @var \Webkul\SampleProduct\Helper\Data
     */
    private $helper;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    private $productAction;

    /**
     * Construct
     *
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param CollectionFactory $collectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Action $productAction
     */
    public function __construct(
        \Webkul\SampleProduct\Helper\Data $helper,
        CollectionFactory $collectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Action $productAction = null
    ) {
        $this->helper = $helper;
        $this->collectionFactory = $collectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productAction = $productAction;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        try {
            $isEnable = $this->helper->isSampleProductEnable();
            $sampleProductIds = $this->collectionFactory->create()->getAllSampleProductIds();
            if ($isEnable) {
                $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                $this->productAction->updateAttributes($sampleProductIds, ['status' => $status], 1);
            } else {
                $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
                $this->productAction->updateAttributes($sampleProductIds, ['status' => $status], 1);
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
