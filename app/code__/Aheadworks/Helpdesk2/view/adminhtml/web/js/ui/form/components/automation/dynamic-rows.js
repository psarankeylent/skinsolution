define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                currentParam: '${ $.paramProvider }:value',
                paramDataScope: ''
            },
            listens: {
                currentParam: 'onParamChange'
            }
        },
        isInitialized: false,

        /**
         * On param change handler
         */
        onParamChange: function () {
            if (this.isInitialized) {
                this.recordData([]);
                this.reload();
                this.showSpinner(false);
            }
            this.isInitialized = true;
        },

        /**
         * @inheritdoc
         */
        checkSpinner: function (elems) {
            this.showSpinner(false);
        },
    });
});
