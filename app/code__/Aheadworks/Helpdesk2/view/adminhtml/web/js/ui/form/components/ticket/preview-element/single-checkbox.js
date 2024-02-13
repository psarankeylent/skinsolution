define([
    'underscore',
    'Magento_Ui/js/form/element/single-checkbox',
    'mage/translate',
    './abstract'
], function (_, SingleCheckbox, $t, AbstractPreview) {
    'use strict';

    return SingleCheckbox.extend(AbstractPreview).extend({
        defaults: {
            requestUrl: '',
            payload: []
        },

        /**
         * @inheritDoc
         */
        setInitialValue: function () {
            this.on('value', this.onValueChange.bind(this));
            return this._super();
        },

        /**
         * Handler for change value
         */
        onValueChange: function() {
            this.save();
        },

        /**
         * @inheritDoc
         */
        getPreview: function () {
            return Boolean(JSON.parse(this.value())) ? $t('Yes') : $t('No');
        }
    });
});
