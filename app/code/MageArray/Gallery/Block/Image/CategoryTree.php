<?php
namespace MageArray\Gallery\Block\Image;

/**
 * Class CategoryTree
 * @package MageArray\Gallery\Block\Image
 */
class CategoryTree extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \MageArray\Gallery\Helper\Data
     */
    protected $_dataHelper;
    protected $_categories;
    protected $_categoryFactory;
    /**
     * CategoryTree constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageArray\Gallery\Model\CategoryFactory $categoryFactory
     * @param \MageArray\Gallery\Model\Categories $categories
     * @param \MageArray\Gallery\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory,
        \MageArray\Gallery\Model\Categories $categories,
        \MageArray\Gallery\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->_categoryFactory = $categoryFactory;
        $this->_dataHelper = $dataHelper;
        $this->_categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getCategoryTree()
    {
        return $this->_categories->getfrontOptionArray();
    }
    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        $mains = $this->_dataHelper->getPageUrl();
        return $mains;
    }

    /**
     * @param $categoryUrl
     * @return string
     */
    public function getCategoryUrl($categoryUrl)
    {
        $categoryPrefix = $this->_dataHelper->getCategoryUrlPrifix();
        $categorySuffix = '.' . $this->_dataHelper->getCategoryUrlSuffix();
        return $this->getUrl(
            //$categoryPrefix . '/' . $categoryUrl . '' . $categorySuffix
            $categoryPrefix . '/' . $categoryUrl
        );
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getNotFoundImage()
    {
        return  $this->getViewFileUrl("MageArray_Gallery::images/not-found.png");
    }
}
