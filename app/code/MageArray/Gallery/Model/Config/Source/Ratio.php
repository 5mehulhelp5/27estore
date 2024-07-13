<?php
namespace MageArray\Gallery\Model\Config\Source;

/**
 * Class Ratio
 * @package MageArray\Gallery\Model\Config\Source
 */
class Ratio implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'Yes'],
            ['value' => '0', 'label' => 'No']
        ];
    }
}
