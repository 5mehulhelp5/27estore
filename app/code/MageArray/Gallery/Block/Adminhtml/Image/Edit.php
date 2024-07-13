<?php
namespace MageArray\Gallery\Block\Adminhtml\Image;

/**
 * Class Edit
 * @package MageArray\Gallery\Block\Adminhtml\Image
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'image_id';
        $this->_blockGroup = 'MageArray_Gallery';
        $this->_controller = 'adminhtml_image';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Image'));
        $this->buttonList->update('delete', 'label', __('Delete Image'));
        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ],
                    ],
                ],
            ],
            10
        );
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }
}
