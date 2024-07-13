<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        Context $context,
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
        $resultPage->addBreadcrumb(__('Image'), __('Image'));
        $resultPage->addBreadcrumb(__('Manage Images'), __('Manage Images'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Images'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
