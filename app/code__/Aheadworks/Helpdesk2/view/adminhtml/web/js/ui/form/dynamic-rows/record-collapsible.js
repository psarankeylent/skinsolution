define([
    'Magento_Ui/js/dynamic-rows/record'
], function (Record) {
    'use strict';

    return Record.extend({
        defaults: {
            showDeleteButton: true
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe([
                    'showDeleteButton'
                ]);

            return this;
        },

        /**
         * Check is need to show delete button for the record
         *
         * @returns {bool}
         */
        isNeedToShowDeleteButton: function () {
            return this.showDeleteButton();
        }
    });
});
