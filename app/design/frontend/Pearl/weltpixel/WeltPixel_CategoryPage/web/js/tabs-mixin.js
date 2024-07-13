define(['jquery'], function($) {
    'use strict';

    return function(tabsMixin) {
        $.widget('mage.tabs', $.mage.tabs, {
            _create: function () {
                this._super();
                if (this.element.hasClass('product data')) {
                    this.element.children().each(function () {
                        if (jQuery(this).hasClass('data item content')) {
                            jQuery(this).removeAttr('aria-labelledby');
                        }
                        if (jQuery(this).attr('role') == 'tabpanel') {
                            jQuery(this).attr('role', 'tab');
                        }
                    });
                }
            }
        });
        return $.mage.tabs;
    }
});
