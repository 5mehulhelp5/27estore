<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

/**
 * Class NewAction
 * @package MageArray\Gallery\Controller\Adminhtml\Image
 */
class NewAction extends \MageArray\Gallery\Controller\Adminhtml\Image
{

    /**
     *
     */

    protected $_imageFactory;

    /**
     *
     */
    public function execute()
    {
        $model = $this->_imageFactory->create();
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('image_post', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::images');
    }
}
