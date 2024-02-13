define([
    'underscore',
    'jquery'
], function (_, $) {
    "use strict";

    return {

        /**
         * Add success message
         *
         * @param message
         * @param timeout
         * @return {exports}
         */
        addSuccessMessage: function (message, timeout) {
            var self = this;
            this
                .clear()
                ._addMessage(message, false);

            if (timeout) {
                this._clearByTimeout(timeout);
            }

            return this;
        },

        /**
         * Add error message
         *
         * @param message
         * @param timeout
         * @return {exports}
         */
        addErrorMessage: function (message, timeout) {
            this
                .clear()
                ._addMessage(message, true);

            if (timeout) {
                this._clearByTimeout(timeout);
            }

            return this;
        },

        /**
         * Clear notifications
         *
         * @return {exports}
         */
        clear: function () {
            $('body').notification('clear');

            return this;
        },

        /**
         * Clear notifications by timeout
         *
         * @param timeout
         * @private
         */
        _clearByTimeout: function (timeout) {
            var self = this;
            window.setTimeout(function () {
                self.clear();
            }, Number(timeout));
        },

        /**
         * Add message
         *
         * @param message
         * @param isError
         * @return {exports}
         * @private
         */
        _addMessage: function (message, isError) {
            $('body').notification(
                'add',
                {
                    error: Boolean(isError || false),
                    message: message,
                    insertMethod: function (message) {
                        var $wrapper = $('<div/>').html(message);
                        $('.page-main-actions').after($wrapper);
                    }
                }
            );

            return this;
        }
    }
});
