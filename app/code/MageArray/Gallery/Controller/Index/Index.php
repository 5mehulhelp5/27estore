<?php
namespace MageArray\Gallery\Controller\Index;

use Magento\Framework\App\RequestInterface;

/**
 * Class Index
 * @package MageArray\Gallery\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageArray\Gallery\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_dataHelper->isEnabled()) {
            $this->_redirect('404');
        }
        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
