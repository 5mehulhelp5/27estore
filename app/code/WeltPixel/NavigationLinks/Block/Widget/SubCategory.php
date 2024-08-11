<?php
namespace WeltPixel\NavigationLinks\Block\Widget;

use \Magento\Framework\View\Element\Template\Context;

class SubCategory extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $selectedCategory;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct( \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                 Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_categoryFactory = $categoryFactory;
        $this->selectedCategory = null;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/subcategories.phtml');
    }


    /**
     * @param $category
     * @return bool
     */
    protected function _getCategoryIdFrom($category)
    {
        $value = explode('/', $category);
        $categoryId = false;

        if (isset($value[0]) && isset($value[1]) && $value[0] == 'category') {
            $categoryId = $value[1];
        }

        return $categoryId;
    }

    /**
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getSelectedCategory()
    {
        if (!$this->selectedCategory) {
            $categoryId = $this->_getCategoryIdFrom($this->getData('category'));
            if (!$categoryId) {
                return null;
            }
            $selectedCategory = $this->_categoryFactory->create()->load($categoryId);
            $this->selectedCategory = $selectedCategory;
        }
        return $this->selectedCategory;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        $selectedCategory = $this->getSelectedCategory();
        if (!$selectedCategory) {
            return $identities;
        }
        $subcategoriesLayout = $selectedCategory->getData('weltpixel_sc_layout');

        if ($subcategoriesLayout == \WeltPixel\NavigationLinks\Model\Attribute\Source\CategoryLayout::LAYOUT_IMAGES) {
            $childCategories = $selectedCategory->getChildrenCategories();
            foreach ($childCategories as $category) {
                $identities = array_merge($identities, $category->getIdentities());
            }
        }
        return $identities;
    }



}
