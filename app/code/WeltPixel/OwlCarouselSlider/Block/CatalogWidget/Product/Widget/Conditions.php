<?php
namespace WeltPixel\OwlCarouselSlider\Block\CatalogWidget\Product\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Conditions extends \Magento\CatalogWidget\Block\Product\Widget\Conditions
{
    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->registry->register('weltpixel_owlcarousel_widget_condition', 'allow_quantity ');
        return parent::render($element);
    }}
