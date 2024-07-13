<?php
namespace MageArray\Gallery\Block\Adminhtml;

/**
 * Class Image
 * @package MageArray\Gallery\Block\Adminhtml
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_image';
        $this->_blockGroup = 'MageArray_Gallery';
        $this->_headerText = __('Manage Images');
        $this->_addButtonLabel = __('Add New Image');
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'import_location',
            [
                'label' => __('Import Image'),
                'onclick' => 'setLocation(\'' . $this->_getImportUrl() . '\')',
                'class' => 'primary import_location'
            ]
        );

        return parent::_prepareLayout();
    }

    protected function _getImportUrl()
    {
        return $this->getUrl(
            'gallery/Image/importimage'
        );
    }
}
