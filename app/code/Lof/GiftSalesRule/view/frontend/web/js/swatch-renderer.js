/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Lof ElasticSuite to newer
 * versions in the future.
 *
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */

define([
    'jquery',
    'jquery/ui',
    'Magento_Swatches/js/swatch-renderer'
], function ($) {
    'use strict';

    $.widget('mage.lofGiftRulesSwatchRenderer', $.mage.SwatchRenderer, {
        /**
         * Redefine the input by adapting the name and id.
         * @param config
         * @returns {string}
         * @private
         */
        _RenderFormInput: function (config) {
            let productId = this.element.parents('.product-item-details').attr('data-product-id');

            return '<input class="' + this.options.classes.attributeInput + ' super-attribute-select" ' +
                'name="products[' + productId + '][super_attribute][' + config.id + ']" ' +
                'type="text" ' +
                'value="" ' +
                'data-selector="super_attribute[' + config.id + ']" ' +
                'data-validate="{required: true}" ' +
                'aria-required="true" ' +
                'aria-invalid="false">';
        }
    });

    return $.mage.lofGiftRulesSwatchRenderer;
});
