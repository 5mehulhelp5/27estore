<?php
namespace MageArray\Gallery\Model\Category;

/**
 * Class Category
 * @package MageArray\Gallery\Model\Category
 */
class Category extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Category constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageArray\Gallery\Model\CategoryFactory $categoryFactory
     */
    protected $_categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\Gallery\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * @return mixed
     */
    public function getOptionArray()
    {
        $categoryModel = $this->_categoryFactory->create();
        $categoryDetail = $categoryModel->getCollection();
        $categoryData = [];
        foreach ($categoryDetail as $detail) {
            $categoryData[$detail['category_id']] = $detail['title'];
        }
        return $categoryData;
    }

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
}
