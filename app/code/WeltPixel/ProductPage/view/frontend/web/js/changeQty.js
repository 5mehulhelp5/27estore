define([
    'ko',
    'uiComponent',
    'jquery'
], function (ko, Component, $) {
    'use strict';
    return Component.extend({
        initialize: function () {
            this._super();
            this.qty = ko.observable($(this.qtyInput).val() * 1);
            this.maxQtyValue = $(this.qtyInput).attr('max');
            this.lowerLimit = this.allowZeroQty ? 0 : 1;
        },
        decreaseQty: function() {
            if ( $(this.qtyInput).attr('disabled') == 'disabled') {
                return;
            }
            var newQty = this.qty() - 1;

            if (newQty < this.lowerLimit)
            {
                newQty = this.lowerLimit;
            }
            this.qty(newQty);
        },
        increaseQty: function() {
            if ( $(this.qtyInput).attr('disabled') == 'disabled') {
                return;
            }
            var newQty = this.qty() + 1;
            if (newQty <= this.maxQtyValue) {
                this.qty(newQty);
            }
        }
    });

});
