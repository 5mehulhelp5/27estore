<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

/**
 * Class MassDelete
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class MassDelete extends \Magento\Backend\App\Action
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
                $imageObj = $this->_objectManager->get(\MageArray\Gallery\Model\ResourceModel\Image\CollectionFactory::Class);
                foreach ($eventsIds as $imageId) {
                    $image =  $imageObj->create()->addFieldToFilter('image_id', $imageId);
                    $image->walk('delete');
                }
                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
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

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
