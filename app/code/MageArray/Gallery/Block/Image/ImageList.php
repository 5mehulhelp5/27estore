<?php
namespace MageArray\Gallery\Block\Image;

/**
 * Class ImageList
 * @package MageArray\Gallery\Block\Image
 */
class ImageList extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \MageArray\Gallery\Model\ImageFactory
     */
    protected $_imageFactory;
    /**
     * @var \MageArray\Gallery\Model\CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var \MageArray\Gallery\Model\Categories
     */
    protected $_categories;
    /**
     * @var
     */
    protected $url;
    protected $_storeManager;

    /**
     * ImageList constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageArray\Gallery\Model\ImageFactory $imageFactory
     * @param \MageArray\Gallery\Helper\Data $dataHelper
     * @param \MageArray\Gallery\Model\CategoryFactory $categoryFactory
     * @param \MageArray\Gallery\Model\Categories $categories
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Gallery\Model\ImageFactory $imageFactory,
        \MageArray\Gallery\Helper\Data $dataHelper,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory,
        \MageArray\Gallery\Model\Categories $categories
    ) {
        $this->_imageFactory = $imageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_categories = $categories;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context);
    }

    /**
     * @param $id
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getImages($id)
    {
        $collection = $this->_imageFactory->create()->getCollection();
        if ($id) {
            $collection->addFieldToFilter(
                'image_category',
                [
                    'finset' => $id
                ]
            );
        }
        $storeId = $this->_storeManager->getStore()
            ->getStoreId();
        $collection->addFieldToFilter('status', 'enable');
        $collection->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['eq' => 0]
            ]
        );
        $collection->setOrder('sort_order', 'ASC');
        return $collection;
    }

    public function getImegesMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). 'Gallery';
    }

    /**
     * @param $id
     * @return $this
     */
    public function getGalleryCategories($id)
    {
        return $this->_categoryFactory->create()->load($id);
    }

    /**
     * @return $this
     */
    public function getCategories()
    {
        $storeId = $this->_storeManager->getStore()->getStoreId();
        return $this->_categoryFactory->create()->getCollection()
            ->addFieldToFilter('status', 'enable')
            ->addFieldToFilter('store_id', [['finset' => $storeId], ['eq' => 0]])
            ->setOrder('sort_order', 'ASC');
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->getUrl(
            'pub/media',
            [
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getDisplayMode()
    {
        return $this->_dataHelper->getDisplayMode();
    }

    /**
     * @return mixed
     */
    public function getThumbWidth()
    {
        return $this->_dataHelper->getThumbWidth();
    }

    /**
     * @return mixed
     */
    public function getThumbHeight()
    {
        return $this->_dataHelper->getThumbHeight();
    }

    /**
     * @return mixed
     */
    public function getAspectRatio()
    {
        return $this->_dataHelper->getAspectRatio();
    }

    /**
     * @return mixed
     */
    public function getKeepFrame()
    {
        return $this->_dataHelper->getKeepFrame();
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        return $this->_dataHelper->getPageUrl();
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_dataHelper->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getBackgroundColor()
    {
        return $this->_dataHelper->getBackgroundColor();
    }

    /**
     * @return mixed
     */
    public function getSpaceImages()
    {
        return $this->_dataHelper->getSpaceImages();
    }

    public function getBaseUrlMedia()
    {
        return $this->_dataHelper->getBaseUrlMedia();
    }

    /**
     * @return mixed
     */
    public function getCategoryTree()
    {
        return $this->_categories->getfrontOptionArray();
    }

    /**
     * @param $item
     * @param $width
     * @param null $height
     * @return bool|string
     */
    public function getImageUrl($item)
    {
        return $this->_dataHelper->resize($item);
    }

    /**
     * @param $item
     * @param $width
     * @param null $height
     * @return bool|string
     */
    public function getCategoryImage($item)
    {
        return $this->_dataHelper->categoryImageResize($item);
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    /**
     * @param $categoryUrl
     * @return string
     */
    public function getCategoryUrl($categoryUrl)
    {
        $categoryPrefix = $this->_dataHelper->getCategoryUrlPrefix();
        $categorySuffix = '.' . $this->_dataHelper->getCategoryUrlSuffix();
        return $this->getUrl(
            //$categoryPrefix . '/' . $categoryUrl . '' . $categorySuffix
            $categoryPrefix . '/' . $categoryUrl
        );
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $metaTitle = $this->_dataHelper->getMetaTitle();
        $metaKeywords = $this->_dataHelper->getMetaKeyword();
        $metaDescription = $this->_dataHelper->getMetaDescription();
        $pageTitle = $this->_dataHelper->getPageTitle();

        if ($metaTitle) {
            $this->pageConfig->getTitle()->set($metaTitle);
        } else {
            $this->pageConfig->getTitle()->set(__('Gallery'));
        }

        if ($metaKeywords) {
            $this->pageConfig->setKeywords($metaKeywords);
        } else {
            $this->pageConfig->setKeywords(__('Gallery'));
        }

        if ($metaDescription) {
            $this->pageConfig->setDescription($metaDescription);
        } else {
            $this->pageConfig->setDescription(__('Gallery'));
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($pageTitle);
        } else {
            $pageMainTitle->setPageTitle(__('Gallery'));
        }
        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getNotFoundImage()
    {
        return  $this->getViewFileUrl("MageArray_Gallery::images/not-found.png");
    }
}
