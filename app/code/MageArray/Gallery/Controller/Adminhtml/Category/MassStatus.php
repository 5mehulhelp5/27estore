<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

/**
 * Class MassStatus
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @return $this
     */
    public function execute()
    {
        $categoriesIds = $this->getRequest()->getParam('category');
        if (!is_array($categoriesIds) || empty($categoriesIds)) {
            $this->messageManager->addError(__('Please select Categories.'));
        } else {
            try {
                $status = $this->getRequest()->getParam('status');
                foreach ($categoriesIds as $catId) {
                    $category = $this->_objectManager
                       ->get(\MageArray\Gallery\Model\Category::Class)
                        ->load($catId);
                    $category->setStatus($status);
                    $category->save();
                }
                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been updated.',
                        count($categoriesIds)
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
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::categories'
        );
    }
}
