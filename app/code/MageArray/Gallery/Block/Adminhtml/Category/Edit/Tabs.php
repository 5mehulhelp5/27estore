<?php
namespace MageArray\Gallery\Block\Adminhtml\Category\Edit;

/**
 * Class Tabs
 * @package MageArray\Gallery\Block\Adminhtml\Category\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
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
                        \MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab\General::Class
                    )
                    ->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'main_section1',
            [
                'label' => __('Visibility'),
                'title' => __('Visibility'),
                'content' => $this->getLayout()
                    ->createBlock(
                        \MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab\Visibility::Class
                    )
                    ->toHtml(),
                'active' => false
            ]
        );
    }
}
