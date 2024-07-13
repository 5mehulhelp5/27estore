
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SampleProduct
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define(["jquery"], function ($) {
    "use strict";

    return function (widget) {
        $.widget("mage.configurable", widget, {
            /**
             * Initialize tax configuration, initial settings, and options values.
             * @private
             */
            _initializeOptions: function () {
                var element;

                element = $(this.options.priceHolderSelector);
                if (!element.data("magePriceBox")) {
                    element.priceBox();
                }

                return this._super();
            },
        });

        return $.mage.configurable;
    };
});
