<?php

namespace Webkul\ImageGallery\Block\Adminhtml\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Save extends Generic implements ButtonProviderInterface
{
    /**
     * ButtonData
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Image'),
            'class' => 'primary save',
            'on_click' => '',
            'sort_order' => 50,
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'imagegallery_image_form.imagegallery_image_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'save_and_continue' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

            ],
        ];
    }
}
