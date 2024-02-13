define([
    'Magento_Ui/js/form/element/text'
], function (Text) {
    'use strict';

    return Text.extend({
        defaults: {
            elementTmpl: 'Aheadworks_Helpdesk2/ui/form/element/ticket/link',
            isLinkActive: true,
            text: 'URL',
            url: '',
            target: 'self'
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super();
            this.observe(['text', 'url', 'error', 'warn']);

            return this;
        },

        /**
         * Retrieve label for column
         *
         * @returns {String}
         */
        getText: function() {
            return this.text();
        },

        /**
         * Retrieve url for column
         *
         * @returns {String}
         */
        getUrl: function() {
            return this.url();
        }
    });
});
