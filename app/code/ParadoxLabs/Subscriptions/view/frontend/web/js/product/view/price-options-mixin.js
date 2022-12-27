/*jshint jquery:true*/
define([
    'jquery',
    'priceUtils',
    'mage/translate'
], function ($, utils) {
    "use strict";

    return function (widget) {
        $.widget('mage.priceOptions', widget, {
            _applyOptionNodeFix: function (options) {
                // Render normal custom option labels/prices
                this._super(options);

                // Render subscription adjustment pricing
                this._renderAdjustmentPrices(options);
            },

            /**
             * Add subscription adjustment prices to custom option labels on product view (when applicable)
             */
            _renderAdjustmentPrices: function(options) {
                var config = this.options;

                options.filter('select, .radio').each(function (index, element) {
                    var $element = $(element),
                        optionId = utils.findOptionId($element),
                        optionConfig = config.optionConfig && config.optionConfig[optionId];

                    if ($element.hasClass('radio')) {
                        this._processOption($element, optionConfig);
                    } else {
                        var options = $element.find('option');
                        for (var key = 0; key < options.length; key++) {
                            this._processOption(options[key], optionConfig);
                        }
                    }
                }.bind(this));
            },

            /**
             * Process a particular input option
             */
            _processOption: function (option, optionConfig) {
                var $option,
                    optionValue,
                    prices,
                    message;

                $option = $(option);
                optionValue = $option.val();

                if (!optionValue && optionValue !== 0) {
                    return;
                }

                prices = optionConfig[optionValue] ? optionConfig[optionValue].prices : null;

                if (prices === null
                    || prices['finalPrice'] === undefined
                    || prices['finalPrice']['adjustments'] === undefined
                    || prices['finalPrice']['adjustments']['subscription'] === undefined) {
                    return;
                }

                var $label = $option;
                if ($option.prop('type') === 'radio') {
                    $label = $option.siblings('label');
                }

                // Note, the price is backwards (positive is negative) because of how core uses
                // adjustment prices in the product-view dynamic calculations.
                if (prices['finalPrice']['adjustments']['subscription'] < 0) {
                    this._renderAdjustmentPrice(
                        $label,
                        $.mage.__('%1$s (+%2$s setup fee)'),
                        prices,
                        this.options.optionConfig
                    );
                } else if (prices['finalPrice']['adjustments']['subscription'] > 0) {
                    this._renderAdjustmentPrice(
                        $label,
                        $.mage.__('%1$s (-%2$s first order discount)'),
                        prices,
                        this.options.optionConfig
                    );
                }
            },

            /**
             * Add a particular adjustment price to its option's label
             */
            _renderAdjustmentPrice: function($option, message, prices, config) {
                message = message.replace('%1$s', $option.text());
                message = message.replace(
                    '%2$s',
                    utils.formatPrice(
                        Math.abs(prices['finalPrice']['adjustments']['subscription']),
                        config.priceFormat
                    )
                );

                $option.text(message);
            }
        });

        return $.mage.priceOptions;
    }
});
