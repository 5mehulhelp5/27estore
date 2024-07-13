<?php
namespace MageArray\Gallery\Block\Adminhtml\Category;

/**
 * Class Edit
 * @package MageArray\Gallery\Block\Adminhtml\Category
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'category_id';
        $this->_blockGroup = 'MageArray_Gallery';
        $this->_controller = 'adminhtml_category';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Category'));
        $this->buttonList->update('delete', 'label', __('Delete Category'));
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
