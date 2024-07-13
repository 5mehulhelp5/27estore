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
namespace Webkul\SampleProduct\Plugin;

use Magento\Checkout\Model\Cart;

class PreventAddToCart
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
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\SampleProduct\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\SampleProduct\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritdoc
     */
    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null)
    {
        $isCustomerGroupAllowed = 1;
        if ($this->customerSession->isLoggedIn()) {
            $customerData = $this->customerSession->getData();
            $customerGroupId = $customerData['customer_group_id'];

            $allowCustomerGroups = explode(',', $this->helper->allowCustomerGroups());

            if (!in_array($customerGroupId, $allowCustomerGroups)) {
                $isCustomerGroupAllowed = 0;
            }
        }

        if (1==0 && !$isCustomerGroupAllowed) {
            $this->checkoutSession->setQuoteId(null);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("This Customer group is not allowed to buy this products")
            );
        }
        return [$productInfo,$requestInfo];
    }
}
