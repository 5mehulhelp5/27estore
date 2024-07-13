<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Model;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\Driver\File;

class ImageUploader
{
    /**
     * @var Database
     */
    private $coreFileStorageDatabase;
    /**
     * @var Filesystem
     */
    private $mediaDirectory;
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SetBaseTmpPath
     */
    public $baseTmpPath;
    /**
     * @var SetBasePath
     */
    public $basePath;
    /**
     * @var AllowedExtensions
     */
    public $allowedExtensions;

    /**
     * @var Magento\Framework\Filesystem\Driver\File
     */
    public $_fileDriver;

    /**
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param File $fileDriver
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        File $fileDriver,
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = "label/icon";
        $this->basePath = "label/icon";
        $this->allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        $this->_fileDriver = $fileDriver;
    }
    /**
     * SetBaseTmpPath.
     *
     * @param string $baseTmpPath
     * @return this
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }
    /**
     * SetBasePath.
     *
     * @param string $basePath
     * @return this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
    /**
     * SetAllowedExtensions.
     *
     * @param string $allowedExtensions
     * @return this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }
    /**
     * GetBaseTmpPath.
     *
     * @return this
     */
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }
    /**
     * GetBasePath.
     *
     * @return this
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    /**
     * GetAllowedExtensions.
     *
     * @return this
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }
    /**
     * GetFilePath.
     *
     * @param string $path
     * @param string $imageName
     * @return this
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }
    /**
     * MoveFileFromTmp.
     *
     * @param string $imageName
     * @return this
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);
        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }
    /**
     * SaveFileToTmpDir.
     *
     * @param string $fileId
     * @return this
     */
    public function saveFileToTmpDir($fileId)
    {
        //$baseTmpPath = $this->getBaseTmpPath();
        $baseTmpPath = 'imagegallery/images/';
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        if (!$result) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];
        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
        return $result;
    }
}
