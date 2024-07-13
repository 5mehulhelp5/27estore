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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Checks if guest checkout is allowed then quote contains sample products.
 */
class IsAllowedGuestCheckoutObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Webkul\SampleProduct\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    private $_urlFactory;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Response\Http $response,
        \Webkul\SampleProduct\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger = null
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->_urlFactory = $urlFactory;
        $this->response = $response;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * Check is allowed guest checkout if quote contain sample product(s)
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        try {
            if (!$this->helper->isSampleProductEnable()) {
                return $this;
            }
            if (!$this->helper->allowToLoginCustomer()) {
                return $this;
            }
            /** Check if customer group is allowed */
            $isCustomerGroupAllowed = 1;
            if ($this->customerSession->isLoggedIn()) {
                $customerData = $this->customerSession->getData();
                $customerGroupId = $customerData['customer_group_id'];

                $allowCustomerGroups = explode(',', $this->helper->allowCustomerGroups());

                if (!in_array($customerGroupId, $allowCustomerGroups)) {
                    $isCustomerGroupAllowed = 0;
                }
            }

            $result = $observer->getEvent()->getResult();

            /* @var $quote Quote */
            $quote = $observer->getEvent()->getQuote();

            /** If customer group is not allowed for sample products */
            $isSampleProductInCart = 0;
            $count = 0;
            $totalQuoteItem = 0;
            foreach ($quote->getAllItems() as $item) {
                $totalQuoteItem++;
                $product = $item->getProduct();
                if ((string)$product->getTypeId() === 'sample') {
                    $isSampleProductInCart = 1;
                    $count++;
                    if (!$isCustomerGroupAllowed) {
                        $item->delete();
                    }
                    $result->setIsAllowed(false);
                }
            }

            if ($isSampleProductInCart && !$isCustomerGroupAllowed) {
                $this->messageManager->addErrorMessage(
                    __('This Customer group is not allowed to buy this products')
                );
                if ($count == $totalQuoteItem) {
                    $this->checkoutSession->setQuoteId(null);
                    $quote->delete();
                }
                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e);
            $this->checkoutSession->setQuoteId(null);
        }

        return $this;
    }

    /**
     * Creates URL object
     *
     * @return \Magento\Framework\UrlInterface
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }
}
