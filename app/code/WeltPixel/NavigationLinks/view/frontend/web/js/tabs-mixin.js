define(['jquery'], function($) {
    'use strict';

    return function(navigationTabs) {
        $.widget('mage.tabs', $.mage.tabs, {
            _create: function () {
                this._super();
                if (this.element.hasClass('nav-sections-items')) {
                    this.element.children().each(function () {
                        if (jQuery(this).attr('role') == 'tabpanel') {
                            jQuery(this).attr('role', 'tab')
                        }
                    });
                }
            }
        });
        return $.mage.tabs;
    }
});
