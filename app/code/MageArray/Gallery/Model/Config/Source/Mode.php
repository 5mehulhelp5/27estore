<?php
namespace MageArray\Gallery\Model\Config\Source;

/**
 * Class Mode
 * @package MageArray\Gallery\Model\Config\Source
 */
class Mode implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'one_page_all_image',
                'label' => 'One Page All Image'
            ],
            [
                'value' => 'one_page_with_category_filter',
                'label' => 'One Page With Category Filter'
            ],
            [
                'value' => 'multiple_page_with_category_text',
                'label' => 'Multiple Page With Category Text'
            ],
            [
                'value' => 'multiple_page_with_category_Image',
                'label' => 'Multiple Page With Category Image'
            ]
        ];
    }
}
