define([
    'jquery',
    'Magento_Ui/js/form/element/select',
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            isGuestCustomer: true,
            validationParams: null
        },

        /**
         * @inheritDoc
         */
        validate: function () {
            this.validationParams = {
                field: this
            };
            return this._super();
        }
    });
});
