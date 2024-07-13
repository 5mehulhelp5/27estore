/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SampleProduct
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
require([
    'jquery',
    'uiComponent',
    'priceBox'
], function($, Component){
    return Component.extend({
        initialize:function(config) {
            console.log(config)
            var dataPriceBoxSelector = '[data-role=priceBox]',
            dataProductIdSelector = '[data-product-id='+config.product_id+']',
            priceBoxes = $(dataPriceBoxSelector + dataProductIdSelector);

            priceBoxes = priceBoxes.filter(function(index, elem){
                return !$(elem).find('.price-from').length;
            });

            priceBoxes.priceBox({'priceConfig': config.priceConfig});
            }
    })
});