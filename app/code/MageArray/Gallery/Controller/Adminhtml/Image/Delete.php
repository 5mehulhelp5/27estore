<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

/**
 * Class Delete
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class Delete extends \Magento\Backend\App\Action
{

    /**
     * @return $this
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('image_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager
                    ->create(\MageArray\Gallery\Model\Image::Class);
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(
                    __('Image has been deleted.')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['event_id' => $id]
                );
            }
        }
        $this->messageManager->addError(
            __('We can\'t find a Image to delete.')
        );
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
