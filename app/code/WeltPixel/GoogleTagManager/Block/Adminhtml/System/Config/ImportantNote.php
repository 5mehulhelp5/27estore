<?php

namespace WeltPixel\GoogleTagManager\Block\Adminhtml\System\Config;

/**
 * Class SeparatorElement
 * @package WeltPixel\GoogleTagManager\Block\Adminhtml\System\Config
 */
class ImportantNote extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<div>' .
            "<p class='importany-note'>Following the retirement of Google Analytics Universal Analytics, this extension is deprecated. To continue tracking eCommerce metrics on this store, please switch over to the
 <a target='_blank' href='https://www.weltpixel.com/google-analytics-4-ga4-with-gtm-support-for-magento-2.html'>Google Analytics 4 extension</a>.</p>" .
            '</div>';

        return $html;
    }
}
