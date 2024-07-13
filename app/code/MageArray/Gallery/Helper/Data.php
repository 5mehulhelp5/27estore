<?php
namespace MageArray\Gallery\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Data
 * @package MageArray\Gallery\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * To Set Gallery Enabled
     */
    const XML_PATH_ENABLED = 'gallery/general/enable';
    /**
     * To Set Gallery Header Link
     */
    const XML_PATH_HEADER_LINK = 'gallery/general/header_link';
    /**
     * To Set Gallery Header Link
     */
    const XML_PATH_FOOTER_LINK = 'gallery/general/footer_link';
    /**
     * To Set Gallery Page Url
     */
    const XML_PATH_PAGE_URL = 'gallery/general/page_url';
    /**
     * To Set Gallery Link Title
     */
    const XML_PATH_LINK_TITLE = 'gallery/general/link_title';
    /**
     * To Set Gallery Page Title
     */
    const XML_PATH_PAGE_TITLE = 'gallery/general/page_title';
    /**
     * To Set Gallery Page Description
     */
    const XML_PATH_PAGE_DESCRIPTION = 'gallery/general/page_description';
    /**
     * To Set Gallery Meta Title
     */
    const XML_PATH_META_TITLE = 'gallery/general/meta_title';
    /**
     * To Set Gallery Meta KeyWords
     */
    const XML_PATH_META_KEYWORDS = 'gallery/general/meta_keywords';
    /**
     * To Set Gallery Meta Description
     */
    const XML_PATH_META_DESCRIPTION = 'gallery/general/meta_description';
    /**
     * To Set Gallery Display Mode
     */
    const XML_PATH_DISPLAY_MODE = 'gallery/general/display_mode';
    /**
     * To Set Gallery Prefix Url
     */
    const XML_PATH_CATEGORY_URL_PREFIX = 'gallery/general/category_url_prefix';
    /**
     * To Set Gallery suffix Url
     */
    const XML_PATH_CATEGORY_URL_SUFFIX = 'gallery/general/category_url_suffix';
    /**
     *  To Set Gallery Thumb Width
     */
    const XML_PATH_THUMB_WIDTH = 'gallery/general/thumb_width';
    /**
     * To Set Gallery Thumb Height
     */
    const XML_PATH_THUMB_HEIGHT = 'gallery/general/thumb_height';
    /**
     * To Set Gallery Aspect Ratio
     */
    const XML_PATH_ASPECT_RATIO = 'gallery/general/aspect_ratio';
    /**
     * To Set Gallery Keep Frame
     */
    const XML_PATH_KEEP_FRAME = 'gallery/general/keep_frame';
    /**
     * To Set Gallery Background Color
     */
    const XML_PATH_BACKGROUND_COLOR = 'gallery/general/background_color';
    /**
     *   To Set Gallery Space Between Images
     */
    const XML_PATH_SPACE_BETWEEN_IMAGES =
        'gallery/general/space_between_images';
    /**
     * To Set Min Height
     */
    const MIN_HEIGHT = 50;
    /**
     * To Set Max Height
     */
    const MAX_HEIGHT = 1080;
    /**
     * To Set Min Width
     */
    const MIN_WIDTH = 50;
    /**
     * To Set Max Width
     */
    const MAX_WIDTH = 1920;

    /**
     * @var array
     */
    protected $_imageSize = [
        'minheight' => self::MIN_HEIGHT,
        'minwidth' => self::MIN_WIDTH,
        'maxheight' => self::MAX_HEIGHT,
        'maxwidth' => self::MAX_WIDTH,
    ];
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_imageFactory;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_ioFile;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_hexlen = 3;
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        $this->_ioFile = $ioFile;
    }

    /**
     * @param \MageArray\Gallery\Model\Image $item
     * @param $width
     * @param null $height
     * @return bool|string
     * @throws \Exception
     */
    public function resize(
        \MageArray\Gallery\Model\Image $item
    ) {
        $height = $this->getThumbHeight();
        $width = $this->getThumbWidth();
        $keepRatio = $this->getAspectRatio();
        $keepFrame = $this->getKeepFrame();
        $backgroundColor = $this->getBackgroundColor();
        $colorRgb = $this->hex2rgb($backgroundColor);
        $backUrl = str_replace("#", "", $backgroundColor);
        if (!$item->getImage()) {
            return false;
        }
        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }
        $width = (int)$width;

        if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
            return false;
        }
        $height = (int)$height;

        $imageFile = $item->getImage();
        if ($keepRatio == 1) {
            if ($keepFrame == 1) {
                $cacheDir = $this->getBaseDir()
                    . 'cache'
                    . '/'
                    . $width
                    . 'frame';
                $cacheUrl = $this->getBaseUrlMedia()
                    . 'cache' . '/'
                    . $width
                    . 'frame'
                    . '/';
                if ($backUrl) {
                    $cacheDir = $this->getBaseDir()
                        . 'cache'
                        . '/'
                        . $width
                        . '-'
                        . $backUrl
                        . '-'
                        . 'frame';
                    $cacheUrl = $this->getBaseUrlMedia()
                        . 'cache'
                        . '/'
                        . $width
                        . '-'
                        . $backUrl
                        . '-'
                        . 'frame'
                        . '/';
                }
            } else {
                $cacheDir = $this->getBaseDir()
                    . 'cache'
                    . '/'
                    . $width;
                $cacheUrl = $this->getBaseUrlMedia()
                    . 'cache'
                    . '/'
                    . $width
                    . '/';
            }
        } else {
            $cacheDir = $this->getBaseDir()
                . 'cache'
                . '/'
                . $width
                . 'x'
                . $height;
            $cacheUrl = $this->getBaseUrlMedia()
                . 'cache'
                . '/'
                . $width
                . 'x'
                . $height
                . '/';
        }
        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }
        try {
            $image = $this->_imageFactory
                ->create($this->getBaseDir() . 'gallery/' . $imageFile);
            $image = $this->getImageFramData($image);

            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $hex
     * @return array
     */
    public function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == $this->_hexlen) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];
        return $rgb; // returns an array with the rgb values
    }

    /**
     * @param \MageArray\Gallery\Model\Category $item
     * @param $width
     * @param null $height
     * @return bool|string
     * @throws \Exception
     */
    public function categoryImageResize(
        \MageArray\Gallery\Model\Category $item
    ) {
        $height = $this->getThumbHeight();
        $width = $this->getThumbWidth();
        $keepRatio = $this->getAspectRatio();
        $keepFrame = $this->getKeepFrame();
        $backgroundColor = $this->getBackgroundColor();
        $colorRgb = $this->hex2rgb($backgroundColor);
        $backUrl = str_replace("#", "", $backgroundColor);

        if (!$item->getImage()) {
            return false;
        }
        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }
        $width = (int)$width;

        if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
            return false;
        }
        $height = (int)$height;

        $imageFile = $item->getImage();

        if ($keepRatio == 1) {
            if ($keepFrame == 1) {
                $cacheDir = $this->getBaseDir()
                    . 'cache'
                    . '/'
                    . 'category'
                    . $width
                    . 'frame';
                $cacheUrl = $this->getBaseUrlMedia()
                    . 'cache'
                    . '/'
                    . 'category'
                    . $width
                    . 'frame' . '/';
                if ($backUrl) {
                    $cacheDir = $this->getBaseDir()
                        . 'cache'
                        . '/'
                        . 'category'
                        . $width
                        . '-'
                        . $backUrl
                        . '-'
                        . 'frame';
                    $cacheUrl = $this->getBaseUrlMedia()
                        . 'cache'
                        . '/'
                        . 'category'
                        . $width
                        . '-'
                        . $backUrl
                        . '-'
                        . 'frame'
                        . '/';
                }
            } else {
                $cacheDir = $this->getBaseDir()
                    . 'cache'
                    . '/'
                    . 'category'
                    . $width;
                $cacheUrl = $this->getBaseUrlMedia()
                    . 'cache'
                    . '/'
                    . 'category'
                    . $width
                    . '/';
            }
        } else {
            $cacheDir = $this->getBaseDir()
                . 'cache'
                . '/'
                . 'category'
                . $width
                . 'x'
                . $height;
            $cacheUrl = $this->getBaseUrlMedia()
                . 'cache'
                . '/'
                . 'category'
                . $width
                . 'x'
                . $height
                . '/';
        }
        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }
        try {
            $image = $this->_imageFactory->create(
                $this->getBaseDir() . 'gallery/' . $imageFile
            );
            $image = $this->getImageFramData($image);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $storePath
     * @return mixed
     */
    public function getStoreConfig($storePath)
    {
        $storeConfig = $this->_scopeConfig->getValue(
            $storePath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeConfig;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isHeaderLinkEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function isFooterLinkEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaKeyword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_KEYWORDS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_META_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_DESCRIPTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAGE_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getLinkTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LINK_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCategoryUrlPrefix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_URL_PREFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCategoryUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getDisplayMode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getThumbWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMB_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getThumbHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMB_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getAspectRatio()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ASPECT_RATIO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getBackgroundColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BACKGROUND_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getSpaceImages()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SPACE_BETWEEN_IMAGES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getKeepFrame()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_KEEP_FRAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath();
        return $path;
    }

    /**
     * @return mixed
     */
    public function getBaseUrlMedia()
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getImageFramData($image)
    {
        $keepRatio = $this->getAspectRatio();
        $keepFrame = $this->getKeepFrame();
        $backgroundColor = $this->getBackgroundColor();
        $colorRgb = $this->hex2rgb($backgroundColor);
        if ($keepRatio == 1) {
            $image->keepAspectRatio(true);
            if ($keepFrame == 1) {
                $image->keepFrame(true);
                $image->backgroundColor($colorRgb);
            }
        }
        return $image;
    }
}
