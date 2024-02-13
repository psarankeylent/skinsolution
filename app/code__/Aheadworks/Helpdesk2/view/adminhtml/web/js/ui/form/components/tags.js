define([
    'underscore',
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'jquerytokenize'
], function (_, ko, $, Abstract) {
    'use strict';

    /**
     * Update value
     * @param {Object} viewModel
     * @param {String[]} value
     */
    function updateValue(viewModel, value) {
        if (!_.isArray(value)) {
            value = [];
        }
        viewModel.value(value);
    }

    ko.bindingHandlers.awTags = {

        /**
         * Init binding
         * @param  {Object} element
         * @param  {Function} valueAccessor
         * @param  {Object} allBindings
         * @param  {Object} viewModel
         */
        init: function (element, valueAccessor, allBindings, viewModel) {
            if (valueAccessor()) {
                $('#' + element.id).tokenize({

                    /**
                     * Calls callback when event is triggered
                     * @param  {String} value
                     * @param  {String} text
                     * @param  {Object} tokenize
                     */
                    onAddToken: function (value, text, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    },

                    /**
                     * Calls callback when event is triggered
                     * @param  {String} value
                     * @param  {Object} tokenize
                     */
                    onRemoveToken: function (value, tokenize) {
                        updateValue(viewModel, tokenize.select.val());
                    }
                });
            }
        }
    };

    return Abstract.extend({
        defaults: {
            template: 'Aheadworks_Helpdesk2/ui/form/element/tags'
        }
    });
});
