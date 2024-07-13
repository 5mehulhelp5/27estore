<?php
namespace MageArray\Gallery\Controller\Adminhtml\Image;

use MageArray\Gallery\Model\Upload;
use Magento\Framework\App\Filesystem\DirectoryList;

class Import extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $uploadModel;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \MageArray\Gallery\Model\CategoryFactory
     */
    protected $_categoryFactory;

    protected $_category;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory,
        Upload $uploadModel,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        $this->_storeManager = $storeManager;
        $this->uploadModel = $uploadModel;
        $this->_categoryFactory = $categoryFactory;
        $this->csvProcessor = $csvProcessor;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function isArrayEmpty($a)
    {
        foreach ($a as $elm) {
            if (!empty($elm)) {
                return false;
            }
        }
        return true;
    }

    protected function getCategoryCollection()
    {
        if (empty($this->_category)) {
            $this->_category = $this->_categoryFactory->create()->getCollection();
        }
        return $this->_category;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $uploader = $this->_objectManager->create(
            \Magento\MediaStorage\Model\File\Uploader::Class,
            ['fileId' => 'uploadFile']
        );
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $mediaDirectory = $this->_objectManager
            ->get(\Magento\Framework\Filesystem::Class)
            ->getDirectoryRead(DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory
            ->getAbsolutePath('gallery'));
        $csvFile = $result['path'] . '/' . $result['file'];
        $storesList = [];
        $currentStore = $this->_storeManager->getStores();
        foreach ($currentStore as $store) {
            $storesList[] = $store->getStoreId();
        }
        if (is_file($csvFile)) {
            $input = $this->csvProcessor->getData($csvFile);
            $header = null;
            $requiredHeaders = ['title','image','sort_order','status','store_id'];
            foreach ($input as $rowIndex => $dataRow) {
                if (!$header) {
                    $header = $input[0];
                } else {
                    if (count(array_diff($requiredHeaders, $header)) != 0) {
                        $this->messageManager->addError(__("We can't find required columns:" . implode(', ', array_diff($requiredHeaders, $header))));
                        return $resultRedirect->setPath('*/*/');
                    } else {
                        $check = $this->isArrayEmpty($dataRow);
                        if (!$check) {
                            $data[] = array_combine($header, $dataRow);
                        }
                    }
                }
            }
            unset($data['form_key']);
            foreach ($data as $dataKey => $dataValue) {
                foreach ($dataValue as $key => $value) {
                    if ($key == 'image' && !$this->_isValideimage($value)) {
                        $this->messageManager->addError(__("Only JPG, JPEG,PNG and GIF files are allowed in " . $key . " Column"));
                        return $resultRedirect->setPath('*/*/');
                    } elseif (in_array($key, $requiredHeaders) && $value == '') {
                        $this->messageManager->addError(__("Please make sure Column: " . $key . " value is not empty"));
                        return $resultRedirect->setPath('*/*/');
                    }
                }
            }
            if (count($data) > 0) {
                foreach ($data as $dataKey => $dataValue) {
                    $model = $this->_objectManager
                        ->create(
                            \MageArray\Gallery\Model\Image::Class
                        );
                    $model = $this->setModelData($model, $dataValue, $storesList);

                    try {
                        $model->save();
                    } catch (\Magento\Framework\Exception\LocalizedException $e
                    ) {
                        $this->messageManager->addError($e->getMessage());
                        return $resultRedirect->setPath('*/*/');
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addError($e->getMessage());
                        return $resultRedirect->setPath('*/*/');
                    } catch (\Exception $e) {
                        $this->messageManager->addException(
                            $e,
                            __(
                                'Something went wrong while saving the entry.'
                            )
                        );
                        return $resultRedirect->setPath('*/*/');
                    }
                }
                $this->messageManager->addSuccess(
                    __(
                        'Images imported successfully.'
                    )
                );
            } else {
                $this->messageManager->addError(
                    __(
                        'Some Error occur in import , Please try again'
                    )
                );
            }
            return $resultRedirect->setPath('*/*/');
        } else {
            $this->messageManager->addError(
                __(
                    'Some Error occur in import , Please try again'
                )
            );
            return $resultRedirect->setPath('*/*/');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'MageArray_Gallery::import'
        );
    }
    /**
     * @return bool
     */
    protected function _isValideimage($image)
    {
        $imageExtension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $extension = ['jpg', 'jpeg', 'gif', 'png'];
        if (in_array($imageExtension, $extension)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return mix
     */
    protected function _isValidestore($stores_list, $value)
    {
        $temp = [];
        $ids = explode(",", $value);
        foreach ($ids as $id) {
            if (in_array($id, $stores_list)) {
                $temp[] = $id;
            }
        }
        if (count($temp)>0) {
            return implode(',', $temp);
        } else {
            return 0;
        }
    }
    protected function getCategoryIds($valueArray)
    {
        $collection = $this->getCategoryCollection();
        $imageArray = [];
        foreach ($collection as $object) {
            if (in_array($object->getTitle(), $valueArray)) {
                $imageArray[] = $object->getCategoryId();
            }
        }
        return $imageArray;
    }
    protected function setModelData($model, $dataValue, $storesList)
    {
        foreach ($dataValue as $key => $value) {
            if ($key == 'store_id') {
                $storeIds = 0;
                if (!empty($value)) {
                    $storeIds = $this->_isValidestore($storesList, $value);
                }
                $model->setData($key, $storeIds);
            } else {
                $model->setData($key, $value);
            }

            $valueArray = explode(",", $value);
            $imageArray = $this->getCategoryIds($valueArray);

            if (count($imageArray) > 0) {
                $imageCategory = implode(",", $imageArray);
                $model->setData('image_category', $imageCategory);
            }
            unset($imageArray);
        }
        return $model;
    }
}
