<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

/**
 * Class Edit
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class Edit extends \MageArray\Gallery\Controller\Adminhtml\Category
{

    /**
     *
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        $model = $this->_categoryFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getCategoryId()) {
                $this->messageManager->addError(
                    __('This Categories no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('category_post', $model);
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::categories'
        );
    }
}
