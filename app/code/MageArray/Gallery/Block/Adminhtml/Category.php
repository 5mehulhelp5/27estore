<?php
namespace MageArray\Gallery\Block\Adminhtml;

/**
 * Class Category
 * @package MageArray\Gallery\Block\Adminhtml
 */
class Category extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'MageArray_Gallery';
        $this->_headerText = __('Manage Categories');
        $this->_addButtonLabel = __('Add New Category');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'import_location',
            [
                'label' => __('Import Category'),
                'onclick' => 'setLocation(\'' . $this->_getImportUrl() . '\')',
                'class' => 'primary import_location'
            ]
        );

        return parent::_prepareLayout();
    }

    protected function _getImportUrl()
    {
        return $this->getUrl(
            'gallery/category/importcategory'
        );
    }
}
