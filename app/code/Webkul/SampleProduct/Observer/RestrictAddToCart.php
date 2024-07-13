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
use Magento\Framework\App\RequestInterface;

class RestrictAddToCart implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

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
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $_cartRepository;

    /**
     * @var RequestInterface
     */
    protected $_request;
 
    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $_cartRepository
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\SampleProduct\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $_cartRepository,
        RequestInterface $request
    ) {
        $this->_messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->_cartRepository = $_cartRepository;
        $this->_request = $request;
    }
 
    /**
     * Add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
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

            if (!$isCustomerGroupAllowed) {
                $this->clearCart();
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e);
            $this->checkoutSession->setQuoteId(null);
        }

        return $this;
    }

    /**
     * ClearCart
     */
    public function clearCart()
    {
        $cart = $this->checkoutSession
                            ->clearQuote()
                            ->getQuote()
                            ->unsetData('any_custom_quote_attribute')
                            ->removeAllItems();
        $this->_cartRepository->save($cart);
        $this->checkoutSession->replaceQuote($cart);
    }
}
