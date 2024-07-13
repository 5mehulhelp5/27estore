/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SampleProduct
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    'jquery',
    'mage/template'
], function($, mageTemplate) {
    'use strict';
    $.widget('mage.sampleProduct', {
        _create: function () {
            if (parseInt($('#wk-sample-status').val())) {
                var progressTmpl = mageTemplate('#sampleproduct-template'),
                            tmpl;
                tmpl = progressTmpl({
                    data: {}
                });

                $(tmpl).insertAfter('[data-index="wk-sample-status"]');
            } else {
                $('[data-index="wk-sample-title"]').remove();
                $('[data-index="wk-sample-price"]').remove();
                $('[data-index="wk-sample-qty"]').remove();
            }

            $('body').on('change', '#wk-sample-status', function () {
                if (parseInt($(this).val())) {
                    var progressTmpl = mageTemplate('#sampleproduct-template'),
                                tmpl;
                    tmpl = progressTmpl({
                        data: {}
                    });

                    $(tmpl).insertAfter('[data-index="wk-sample-status"]');
                } else {
                    $('[data-index="wk-sample-title"]').remove();
                    $('[data-index="wk-sample-price"]').remove();
                    $('[data-index="wk-sample-qty"]').remove();
                }
            });

            $(document).on('keyup', '#wk-sample-title', function() {
                var div = $('<div></div>');
                div.html($('#wk-sample-title').val());
                if (div.contents().length == 0) {
                    $('.htmlerr').remove()
                    $('#save-button').parent().css({"pointer-events":""})
                }
                div.contents().filter(function(){
                    if(this.nodeType == 1) {
                        $('.htmlerr').remove()
                        $('#wk-sample-title').after('<span class="htmlerr" style="color:red;">Html tags are not allowed.</span>')
                        $('#save-button').parent().css({"pointer-events":"none"})
                    } else {
                        $('.htmlerr').remove()
                        $('#save-button').parent().css({"pointer-events":""})

                    }
                })
            });
            $(document).on('keypress input', '#wk-sample-price, #wk-sample-qty', function(evt) {
                if (evt.which != 8 && evt.which != 0 && evt.which < 48 || evt.which > 57)
                {
                    evt.preventDefault();
                }
            });
            $('#wk-sample-price, #wk-sample-qty').bind('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                if(isNaN(pastedData)){
                    e.preventDefault();
                }
            });
        }
    });
    return $.mage.sampleProduct;
});
