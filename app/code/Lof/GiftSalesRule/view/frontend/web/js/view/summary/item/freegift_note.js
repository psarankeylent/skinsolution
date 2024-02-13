define([
    'Magento_Checkout/js/view/summary/abstract-total'
], function (viewModel) {
    'use strict';
    var quoteFreegiftMessages = window.checkoutConfig.quoteFreegiftMessages;
    var quoteFreegiftIcons = window.checkoutConfig.quoteFreegiftIcons;
    return viewModel.extend({
        defaults: {
            displayArea: 'after_details',
            template: 'Lof_GiftSalesRule/summary/item/details/freegift_note'
        },
        quoteFreegiftMessages: quoteFreegiftMessages,
        quoteFreegiftIcons: quoteFreegiftIcons,

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getValue: function (quoteItem) {
            if (typeof(this.quoteFreegiftMessages) != "undefined" && typeof(quoteItem['item_id']) !="undefined" && typeof(this.quoteFreegiftMessages[quoteItem['item_id']]) != 'undefined') {
                return this.quoteFreegiftMessages[quoteItem['item_id']];
            }

            return null;
        },

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getIcon: function (quoteItem) {
            if (typeof(this.quoteFreegiftIcons) != "undefined" && typeof(quoteItem['item_id']) !="undefined" && typeof(this.quoteFreegiftIcons[quoteItem['item_id']]) != 'undefined') {
                return this.quoteFreegiftIcons[quoteItem['item_id']];
            }

            return null;
        }
    });
});
