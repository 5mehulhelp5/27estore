define(['jquery'], function($) {
    'use strict';

    return function(collapsibleMixin) {
        $.widget('mage.collapsible', $.mage.collapsible, {
            _processPanels: function () {
                this._super();
                if (this.content.parent().hasClass('filter-current')) {
                    this.content.attr({
                        'role': 'tab'
                    });
                    this.content.children('li').attr('role', 'presentation');
                    this.content.removeAttr('aria-hidden');
                }
                if (this.header.hasClass('filter-current-subtitle')) {
                    this.header.removeAttr('aria-level');
                }
                if (this.element.hasClass('filter-options-item')) {
                    this.element.children().each(function () {
                        if (jQuery(this).attr('role') == 'tabpanel') {
                            jQuery(this).attr('role', 'tab');
                        }
                    });
                }
            }
        });
        return $.mage.collapsible;
    }
});
