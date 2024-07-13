<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

/**
 * Class NewAction
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class NewAction extends \MageArray\Gallery\Controller\Adminhtml\Category
{

    /**
     *
     */
    public function execute()
    {
        $model = $this->_categoryFactory->create();
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('category_post', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::categories'
        );
    }
}
