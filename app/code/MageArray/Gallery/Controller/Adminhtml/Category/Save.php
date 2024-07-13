<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Save
 * @package MageArray\Gallery\Controller\Adminhtml\Category
 */
class Save extends \MageArray\Gallery\Controller\Adminhtml\Category
{

    /**
     * @return $this
     */
    public function execute()
    {
        $imageData = [];
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data['store_id'] = $this->_getStoreData($data['store_id']);
            $model = $this->_objectManager
                ->create(\MageArray\Gallery\Model\Category::Class);
            $id = $this->getRequest()->getParam('category_id');
            if ($id) {
                $model->load($id);
            }
            $data['url_key'] = $this->_getUrlData($data['url_key']);
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            }
            if (isset($imageData['delete']) && $imageData['delete'] == '1') {
                //'gallery/' . $data['image'] = '';
                $data['image'] = '';
                unset($imageData['value']);
            }
            if (empty($imageData['value'])) {
                try {
                    $uploader = $this->_objectManager->create(
                        \Magento\Framework\File\Uploader::Class,
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $mediaDirectory = $this->_objectManager
                        ->get(\Magento\Framework\Filesystem::Class)
                        ->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory
                        ->getAbsolutePath('gallery/'));
                    $imageExtension = $uploader->getFileExtension();
                    $extension = ['jpg', 'jpeg', 'gif', 'png'];
                    if ($result['error'] == 0) {
                        if (in_array($imageExtension, $extension)) {
                            $data['image'] = $result['file'];
                        } else {
                            $this->messageManager->addError(
                                __('Uploaded file is not a valid. Only JPG, JPEG,PNG and GIF files are allowed.')
                            );
                            return $resultRedirect->setPath(
                                '*/*/edit',
                                ['category_id' => $model->getId(),
                                        '_current' => true]
                            );
                        }
                    }
                } catch (\Exception $e) {
                    $message = $this->_getErrorMessage($imageData);
                    $this->messageManager->addException($e, $message);
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['category_id' => $model->getId(),
                                '_current' => true]
                    );
                }
            }
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccess(
                    __('The Category Image has been saved.')
                );
                $this->_objectManager->get(\Magento\Backend\Model\Session::Class)
                        ->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['category_id' => $model->getId(),
                                '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the Category Image.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['category_id' => $this->getRequest()
                    ->getParam('category_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::categories'
        );
    }

    protected function _getErrorMessage($data)
    {
        $message = '';
        if (isset($data['delete'])) {
            $message = __('Please Upload New Image File Before Delete Old Image.');
        } else {
            $message = __('Please Upload Image File.');
        }
        return $message;
    }

    protected function _getStoreData($data)
    {
        if (isset($data)) {
            $data = implode(",", $data);
        }
        return $data;
    }
    protected function _getUrlData($data)
    {
        if ($data) {
            $data = preg_replace(
                '/^-+|-+$/',
                '',
                strtolower(preg_replace(
                    '/[^a-zA-Z0-9]+/',
                    '-',
                    $data
                ))
            );
        } else {
            $data = '';
        }
        return $data;
    }
}
