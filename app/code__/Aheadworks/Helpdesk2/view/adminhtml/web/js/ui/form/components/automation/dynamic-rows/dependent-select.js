define([
    'Magento_Ui/js/form/element/select'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                optionSetArray: '${ $.optionSetProvider}',
                currentParam: '${ $.paramProvider }:value'
            },
            listens: {
                currentParam: 'onParamChange'
            }
        },

        /**
         * On param change handler
         *
         * @param {String} param
         */
        onParamChange: function (param) {
            this.setOptions(this.optionSetArray[param]);
        }
    });
});
