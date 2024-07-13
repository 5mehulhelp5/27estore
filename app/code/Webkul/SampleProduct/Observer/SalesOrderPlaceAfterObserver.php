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
use Webkul\SampleProduct\Model\SampleProductOrderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Quote\Model\QuoteRepository;

/**
 * Webkul SampleProduct SalesOrderPlaceAfterObserver Observer Model.
 */
class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\SampleProduct\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * @var QuoteRepository
     */
    private $_quoteRepository;

    /**
     * @var SessionManager
     */
    private $_coreSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $_orderRepository;

    /**
     * @var SampleProductOrderFactory
     */
    private $sampleProductOrderFactory;

    /**
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param QuoteRepository $quoteRepository
     * @param SessionManager $coreSession
     * @param OrderRepositoryInterface $orderRepository
     * @param SampleProductOrderFactory $sampleProductOrderFactory
     */
    public function __construct(
        \Webkul\SampleProduct\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        QuoteRepository $quoteRepository,
        SessionManager $coreSession,
        OrderRepositoryInterface $orderRepository,
        SampleProductOrderFactory $sampleProductOrderFactory
    ) {
        $this->helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteRepository = $quoteRepository;
        $this->_coreSession = $coreSession;
        $this->_orderRepository = $orderRepository;
        $this->sampleProductOrderFactory = $sampleProductOrderFactory;
    }

    /**
     * Sales order save commmit after on order complete state event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $isMultiShipping = $this->_checkoutSession->getQuote()->getIsMultiShipping();
            if ($isMultiShipping) {
                $quoteId = $this->_checkoutSession->getLastQuoteId();
                $quote = $this->_quoteRepository->get($quoteId);
                if ($quote->getIsMultiShipping() == 1 || $isMultiShipping == 1) {
                    $orderIds = $this->_coreSession->getOrderIds();
                    foreach ($orderIds as $ids => $orderIncId) {
                        $lastOrderId = $ids;
                        /** @var $orderInstance Order */
                        $order = $this->_orderRepository->get($lastOrderId);
                        $this->saveOrderedSampleProducts($order, $lastOrderId);

                    }
                }
            } else {
                /** @var $orderInstance Order */
                $order = $observer->getOrder();
                $lastOrderId = $observer->getOrder()->getId();
                $this->saveOrderedSampleProducts($order, $lastOrderId);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __($e->getMessage())
            );
        }
    }
    /**
     * Save Sample Product Order Details in wk_sample_product_order table
     *
     * @param mixed $order
     * @param int $lastOrderId
     */
    public function saveOrderedSampleProducts($order, $lastOrderId)
    {
        $helper = $this->helper;
        foreach ($order->getAllItems() as $item) {
            $itemData = $item->getData();
            $productId = $item->getProductId();
            if ($helper->isCurrentSampleProduct($productId)) {
                $orderedQty = $item->getQtyOrdered();
                // save sample data to custom table
                $sample = $this->sampleProductOrderFactory->create();
                $sample->setSampleId($productId);
                $sample->setOrderId($lastOrderId);
                $sample->setCustomerId($order->getCustomerId());
                $sample->setQty($orderedQty);
                $sample->save();
            }
        }
    }
}
