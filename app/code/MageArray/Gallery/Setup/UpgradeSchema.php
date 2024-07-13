<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageArray\Gallery\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $directoryList;
    protected $file;
    protected $mediaDir;
    //protected $assetRepository;
    /**
     * {@inheritdoc}
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file,
        Filesystem $mediaDir
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->mediaDir = $mediaDir;
    }
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $imageDir =  $this->directoryList->getPath('media') . '/gallery';
            if (!is_dir($imageDir)) {
                $fileDirResult = $this->file->mkdir($imageDir, 0777);
            }
        }
        $setup->endSetup();
    }
}
