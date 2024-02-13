define([
    'Magento_Ui/js/form/components/button',
    'Aheadworks_Helpdesk2/js/action/send-request',
    'Magento_Ui/js/lib/spinner'
], function (Button, sendRequest, spinner) {
    'use strict';

    return Button.extend({
        defaults: {
            verifyLabelVisible: false,
            verifiedSuccessMessage: '',
            error: '',
            responseType: 'code',
            accessType: 'offline',
            approvalPrompt: 'auto',
            scope: 'https%3A%2F%2Fmail.google.com%2F%20email'
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super()
                .observe('verifyLabelVisible')
                .observe('error');
        },

        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            if (action.redirectUrl.indexOf("key") !== -1) {
                action.redirectUrl = action.redirectUrl.substr(0, action.redirectUrl.indexOf("key"));
            }

            this._sendRequest(action);
        },

        /**
         * Send ajax request
         *
         * @param {Object} action
         * @private
         */
        _sendRequest: function(action) {
            var self = this;

            spinner.show();
            sendRequest(action.dataSetUrl, this.source.get('data'))
                .done(function() {
                    self._prepareWindow(action);
                })
                .fail(function(response) {
                    self.error(response);
                })
                .always(function() {
                    spinner.hide();
                }.bind(this));
        },

        /**
         * Prepare request URL
         *
         * @param {Object} action
         * @return {String}
         */
        prepareRequestUrl: function (action) {
            return action.url +
                '?response_type=' + this.responseType +
                '&access_type=' + this.accessType +
                '&client_id=' + this.source.get('data.client_id') +
                '&redirect_uri=' + action.redirectUrl +
                '&state' +
                '&scope=' + this.scope +
                '&approval_prompt=' + this.approvalPrompt;
        },

        /**
         * Prepare window
         *
         * @param {Object} action
         * @private
         */
        _prepareWindow: function (action) {
            var verifyWindow,
                verifiedInfo,
                self = this;

            verifyWindow = window.open(
                self.prepareRequestUrl(action),
                '_blank',
                'resizable, scrollbars, status, top=0, left=0, width=600, height=500'
            );

            verifiedInfo = setInterval(function() {
                verifyWindow.postMessage('verified_info', window.BASE_URL);
            }, 1000);

            window.addEventListener('message',function(event) {
                if (event.data.is_verified === true) {
                    self.disabled(true);
                    self.verifyLabelVisible(true);
                }
                clearInterval(verifiedInfo);
                if (event.data.error !== undefined) {
                    self.error(event.data.error);
                } else {
                    self.error('');
                }
                event.source.close();
            }.bind(self), false);

        },

        /**
         * Is account already verified
         *
         * @returns {Boolean}
         */
        isVerified: function () {
            if (this.source.get('data.is_verified') === true) {
                this.verifyLabelVisible(true);
                this.disabled(true);
            }

            return this.verifyLabelVisible;
        },

        /**
         * Is button disabled
         *
         * @returns {boolean}
         */
        isDisabled: function () {
            return this.source.get('data.is_gateway_saved') === false;
        },

        /**
         * Is error thrown
         *
         * @return {string}
         */
        isErrorThrown: function () {
            return this.error() !== '';
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
