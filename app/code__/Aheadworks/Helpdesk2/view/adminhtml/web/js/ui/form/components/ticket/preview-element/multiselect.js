define([
    'underscore',
    'Magento_Ui/js/form/element/multiselect',
    'mage/utils/compare',
    './abstract'
], function (_, Multiselect, utils, AbstractPreview) {
    'use strict';

    return Multiselect.extend(AbstractPreview).extend({
        defaults: {
            requestUrl: '',
            payload: []
        },

        prevValue: null,

        /**
         * @inheritDoc
         */
        setInitialValue: function () {
            this.on('value', this.onValueChange.bind(this));
            this.prevValue = this.normalizeData(this.value());

            return this._super();
        },

        /**
         * Handler for change value
         */
        onValueChange: function() {
            var value = this.value();

            if (typeof value !== 'undefined'
                && value !== null
                && !utils.equalArrays(value, this.prevValue)
            ) {
                this.save();
            }
            this.prevValue = this.normalizeData(value)
        },

        /**
         * @inheritDoc
         */
        getPreview: function () {
            var multiselectValue = this.value(),
                option,
                result = [];

            _.each(multiselectValue, function (value) {
                option = this.indexedOptions[value];
                result.push(result + option ? option.label : '');
            }, this);

            return result.join(', ');
        }
    });
});
