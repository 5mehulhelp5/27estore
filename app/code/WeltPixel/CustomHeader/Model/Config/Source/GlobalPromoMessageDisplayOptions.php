<?php


namespace WeltPixel\CustomHeader\Model\Config\Source;


use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class GlobalPromoMessageDisplayOptions implements SourceInterface, OptionSourceInterface
{

    /**
     * block type
     */
    const DISPLAY_ALWAYS = 1;
    const DISPLAY_DEFAULT = 0;


    /**
     * Prepare display options.
     *
     * @return array
     */
    public function getAvailableModes()
    {
        return [
            self::DISPLAY_ALWAYS => __('Always'),
            self::DISPLAY_DEFAULT => __('Cookie-based'),
        ];
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach ($this->getAvailableModes() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {
        $options = $this->getAvailableModes();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
