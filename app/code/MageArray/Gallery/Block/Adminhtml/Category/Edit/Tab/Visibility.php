<?php
namespace MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab;

/**
 * Class Visibility
 * @package MageArray\Gallery\Block\Adminhtml\Category\Edit\Tab
 */
class Visibility extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \MageArray\Gallery\Model\Status
     */
    protected $_status;
    protected $_systemStore;

    /**
     * Visibility constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \MageArray\Gallery\Model\Status $status
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \MageArray\Gallery\Model\Status $status
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('category_post');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Visibility')
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
            'status',
            'select',
            [
                'required' => true,
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => $this->_status->getOptionArray(),
            ]
        );

        $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name' => 'store_id[]',
                'label' => __('Store'),
                'title' => __('Store'),
                'required' => true,
                'values'    => $this->_systemStore
                    ->getStoreValuesForForm(false, true),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
