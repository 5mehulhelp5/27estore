var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'WeltPixel_CategoryPage/js/swatch-renderer': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'WeltPixel_CategoryPage/js/configurable': true
            },
            'mage/collapsible': {
                'WeltPixel_CategoryPage/js/collapsible-mixin': true
            },
            'mage/tabs': {
                'WeltPixel_CategoryPage/js/tabs-mixin': true
            },
        }
    }
};
