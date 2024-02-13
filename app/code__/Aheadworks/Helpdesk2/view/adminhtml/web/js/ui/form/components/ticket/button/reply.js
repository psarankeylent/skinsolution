define([
    './request',
    'mageUtils',
    'Aheadworks_Helpdesk2/js/model/compose-payload-from-source'
], function (Button, utils, composePayload) {
    'use strict';

    return Button.extend({

        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            if (this._isValid()) {
                this.loader.show();
                utils.submit({
                    'url': action.requestUrl,
                    'data': composePayload(action.payload || [])
                });
            }
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         *
         * @return {boolean}
         * @private
         */
        _isValid: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');
            return !this.source.get('params.invalid');
        }
    });
});