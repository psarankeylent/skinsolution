define([
    'jquery',
    'Magento_Ui/js/form/form'
], function ($, Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @return {exports}
         * @private
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }

            return this;
        }
    });
});
