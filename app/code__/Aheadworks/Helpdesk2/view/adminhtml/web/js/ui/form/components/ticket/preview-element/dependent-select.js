define([
    './select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            requestUrl: '',
            payload: [],
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
