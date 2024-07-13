<?php
namespace MageArray\Gallery\Block\Adminhtml\Image\Edit;

/**
 * Class Tabs
 * @package MageArray\Gallery\Block\Adminhtml\Image\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('image_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Image Information'));
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('General Information'),
                'title' => __('General Information'),
                'content' => $this->getLayout()
                    ->createBlock(
                        \MageArray\Gallery\Block\Adminhtml\Image\Edit\Tab\General::Class
                    )
                    ->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'main_section2',
            [
                'label' => __('Visibility'),
                'title' => __('Visibility'),
                'content' => $this->getLayout()
                    ->createBlock(
                        \MageArray\Gallery\Block\Adminhtml\Image\Edit\Tab\Visibility::Class
                    )
                    ->toHtml(),
                'active' => false
            ]
        );
    }
}
