<?php
namespace MageArray\Gallery\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

class Import extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $_storeManager;
    /**
     * @var \MageArray\Gallery\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        $this->_storeManager = $storeManager;
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
            return true;
        }
    }

    /**
     * @return $this
     */
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
        $flag = 0;
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
                            \MageArray\Gallery\Model\Category::Class
                        );
                    $model = $this->setModelData($model, $dataValue, $storesList);
                    try {
                        $model->save();
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
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
                        'Categories imported successfully.'
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

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageArray_Gallery::import');
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
            if (!empty($dataValue['url_key'])) {
                $data = preg_replace(
                    '/^-+|-+$/',
                    '',
                    strtolower(preg_replace(
                        '/[^a-zA-Z0-9]+/',
                        '-',
                        $dataValue['url_key']
                    ))
                );
                $model->setData('url_key', $data);
            } else {
                $model->setData('url_key', '');
            }
        }
        return $model;
    }
}
