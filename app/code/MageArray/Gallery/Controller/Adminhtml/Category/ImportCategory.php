<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class ImportCategory
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class ImportCategory extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * ImportCategory constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageArray_Gallery::gallery');
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        $resultPage->addBreadcrumb(__(
            'Import Categories'
        ), __('Import Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend(__(
                'Import Categories'
            ));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::importcategory'
        );
    }
}
