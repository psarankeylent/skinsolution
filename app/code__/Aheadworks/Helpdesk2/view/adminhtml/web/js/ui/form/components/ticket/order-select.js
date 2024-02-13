define([
    'Magento_Ui/js/form/element/select',
    'Aheadworks_Helpdesk2/js/action/send-request'
], function (Select, request) {
    'use strict';

    return Select.extend({
        defaults: {
            service_url: '',
            modules: {
                emailElement: '${ $.parentName }.customer_email'
            },
            imports: {
                emailValue: '${ $.parentName }.customer_email:value'
            },
            listens: {
                emailValue: 'onEmailChange',
            }
        },
        canBeReset: false,

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            if (_.isEmpty(this.options())) {
                this.visible(false);
            }
            this.canBeReset = true;

            return this;
        },

        /**
         * On email change handler
         *
         * @param {String} email
         */
        onEmailChange: function(email) {
            var self = this;

            if (this.canBeReset || _.isEmpty(this.options())) {
                self.value('');
                self.visible(false);

                if (email && !this.emailElement().error()) {
                    request(this.service_url, {customer_email: email})
                        .done(function (response) {
                            if (!response.error) {
                                self.setOptions(response.options);
                                self.visible(true);
                            }
                        });
                }
            }
        }
    });
});
