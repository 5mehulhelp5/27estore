<?php
namespace MageArray\Gallery\Model;

/**
 * Class Categories
 * @package MageArray\Gallery\Model
 */
class Categories extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * For STATUS CAT1
     */
    const STATUS_CAT1 = 1;
    /**
     * For STATUS CAT2
     */
    const STATUS_CAT2 = 2;
    /**
     * For STATUS CAT3
     */
    const STATUS_CAT3 = 3;
    /**
     * @var
     */
    protected $_category;
    protected $_categoryFactory;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * Categories constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasChild($id)
    {
        $categoryModel = $this->_categoryFactory->create();
        $categoryId = $categoryModel->load($id);
        if (count($categoryId) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param int $parent
     * @param int $level
     */
    public function dumpTree($parent = 0, $level = 0)
    {
        $categoryModel = $this->_categoryFactory->create();
        $categoryColl = $categoryModel->getCollection()
            ->addFieldToFilter('status', 1);
        foreach ($categoryColl as $categories) {
            if ($parent == $categories['parent_cat_id']) {
                $this->_category['options']
                [$categories['category_id']] = $categories['title'];
                if ($this->hasChild($categories['category_id'])) {
                    $this->dumpTree($categories['category_id'], $level + 1);
                }
            }
        }
    }

    /**
     * @param int $parent
     * @param int $level
     */
    public function dumpTreedesign($parent = 0, $level = 0)
    {
        $categoryModel = $this->_categoryFactory->create();
        $categoryColl = $categoryModel->getCollection()
            ->addFieldToFilter('status', 1);
        foreach ($categoryColl as $categories) {
            if ($parent == $categories['parent_cat_id']) {
                $this->_category['optionsdesign']
                [$categories['category_id']] =
                    $categories['url_key']
                    . $categories['title'];
                if ($this->hasChild($categories['category_id'])) {
                    $this->dumpTreedesign(
                        $categories['category_id'],
                        $level + 1
                    );
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        if (!isset($this->_category['options'])) {
            $this->_category['options'] = [];
        }
        return $this->_category['options'];
    }

    /**
     * @return mixed
     */
    public function getfrontOptionArray()
    {
        if (empty($this->_category['optionsdesign'])) {
            $this->_category['optionsdesign'] = [];
        }
        return $this->_category['optionsdesign'];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['label' => $value, 'value' => $index];
        }
        return $result;
    }

    /**
     * @param $optionId
     * @return null
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
