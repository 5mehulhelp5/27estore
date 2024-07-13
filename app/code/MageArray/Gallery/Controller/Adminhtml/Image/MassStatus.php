<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

/**
 * Class MassStatus
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class MassStatus extends \Magento\Backend\App\Action
{

    /**
     * @return $this
     */

    public function execute()
    {
        $eventsIds = $this->getRequest()->getParam('image');
        if (!is_array($eventsIds) || empty($eventsIds)) {
            $this->messageManager->addError(__('Please select Image(s).'));
        } else {
            try {
                $status = $this->getRequest()->getParam('status');
                foreach ($eventsIds as $postId) {
                    $statuses = $this->_objectManager
                        ->get(\MageArray\Gallery\Model\Image::Class)
                        ->load($postId);
                    $statuses->setStatus($status)->save();
                }
                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been updated.',
                        count($eventsIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()
            ->setPath('gallery/*/index');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
