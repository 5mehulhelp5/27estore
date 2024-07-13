<?php
namespace MageArray\Gallery\Controller\Category;

use Magento\Framework\App\Action\Context;

/**
 * Class CategoryList
 * @package MageArray\Gallery\Controller\Category
 */
class CategoryList extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;

    /**
     * CategoryList constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \MageArray\Gallery\Helper\Data $dataHelper
     */
    protected $_resultPageFactory;
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageArray\Gallery\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /*
        $page = $this->_resultPageFactory->create(
            false,
            [
                'isIsolated' => true
            ]
        );
        */
        $page = $this->_resultPageFactory->create();
        return $page;
    }
}
