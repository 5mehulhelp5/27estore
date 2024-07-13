<?php
namespace MageArray\Gallery\Model;

/**
 * Class Status
 * @package MageArray\Gallery\Model
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * To Check Enabled
     */
    const STATUS_ENABLE = 'enable';
    /**
     *  To Check Disabled
     */
    const STATUS_DISABLE = 'disable';

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
     * @return array
     */
    public function getOptionArray()
    {
        return [self::STATUS_ENABLE => __('Enable'),
                self::STATUS_DISABLE => __('Disable')];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    /**
     * @param $optionId
     * @return mixed|null
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
