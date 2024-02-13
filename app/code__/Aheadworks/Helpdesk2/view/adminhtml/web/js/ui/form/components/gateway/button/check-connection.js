define([
    'Magento_Ui/js/form/components/button',
    'Aheadworks_Helpdesk2/js/action/send-request',
    'Aheadworks_Helpdesk2/js/action/show-alert-message',
    'Magento_Ui/js/lib/spinner'
], function (Button, sendRequest, showAlertMessage, spinner) {
    'use strict';

    return Button.extend({
        defaults: {
            successCssClass: 'success',
            errorCssClass: 'error',
            messageTitle: 'Test connection'
        },

        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');

            if (!this.source.get('params.invalid')) {
                this._sendRequest(action.checkUrl, this.source.get('data'));
            } else {
                showAlertMessage(
                    this.errorCssClass,
                    this.messageTitle,
                    'Some required fields are not filled up'
                );
            }
        },

        /**
         * Send ajax request
         *
         * @param {String} url
         * @param {Object} data
         *
         * @private
         */
        _sendRequest: function(url, data) {
            var self = this;

            spinner.show();
            sendRequest(url, data)
                .done(function(response) {
                    showAlertMessage(
                        response.error ? self.errorCssClass : self.successCssClass,
                        self.messageTitle,
                        response.message
                    );
                }).fail(function(response) {
                showAlertMessage(self.errorCssClass, self.messageTitle, response.statusText);
            }).always(function() {
                spinner.hide();
            }.bind(this));
        },

        /**
         * Hide element
         *
         * @returns {Button} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Show element
         *
         * @returns {Button} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        }
    });
});
