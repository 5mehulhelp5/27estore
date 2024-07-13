<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

/**
 * Class MassDelete
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class MassDelete extends \Magento\Backend\App\Action
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
                $catgoryObj = $this->_objectManager->get(\MageArray\Gallery\Model\ResourceModel\Category\CollectionFactory::Class);
                foreach ($categoriesIds as $catId) {
                    $category = $catgoryObj->create()->addFieldToFilter('category_id', $catId);
                    $category->walk('delete');
                }
                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
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
