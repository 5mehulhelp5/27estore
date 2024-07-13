<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

/**
 * Class Edit
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class Edit extends \MageArray\Gallery\Controller\Adminhtml\Image
{

    /**
     *
     */
    protected $_imageFactory;

    public function execute()
    {
        $id = $this->getRequest()->getParam('image_id');
        $model = $this->_imageFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getImageId()) {
                $this->messageManager->addError(
                    __('This Image no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('image_post', $model);
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
