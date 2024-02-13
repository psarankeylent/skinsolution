define([
    'underscore',
    'Magento_Ui/js/form/element/select',
    './abstract'
], function (_, Select, AbstractPreview) {
    'use strict';

    return Select.extend(AbstractPreview).extend({
        defaults: {
            requestUrl: '',
            payload: []
        },

        prevValue: null,

        /**
         * @inheritDoc
         */
        setInitialValue: function () {
            this._super();
            this.on('value', this.onValueChange.bind(this));
            this.prevValue = this.value();
            
            return this;
        },

        /**
         * Handler for change value
         */
        onValueChange: function() {
            var value = this.value();

            if (value !== null && value !== this.prevValue) {
                this.save();
            }
            this.prevValue = value;
        }
    });
});
