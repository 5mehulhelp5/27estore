<?php
namespace MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab;

/**
 * Class General
 * @package MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('category_post');
        $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information')
            ]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'category_id',
                'hidden',
                [
                    'name' => 'category_id'
                ]
            );
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'name' => 'title',
            ]
        );

        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addType(
            'image',
            \MageArray\Gallery\Block\Adminhtml\Category\Form\Renderer\Customfield::Class
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'title' => __('Image'),
                'label' => __('Image'),
                'required' => true,
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'required-entry',
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Image Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Image Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
