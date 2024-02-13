define([
    'jquery',
    'uiElement'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            contactFormSelector: '#contact-form',
            modules: {
                form: 'aw_helpdesk2_form'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            this._initFormValidator();

            return this;
        },

        /**
         * Validate help desk fields
         *
         * @return {exports}
         * @private
         */
        _initFormValidator: function () {
            var self = this;

            $(this.contactFormSelector).on('submit', function(event) {
                self._disableSubmitButton();
                self.source.set('params.invalid', false);
                self.source.trigger('data.validate');
                if (!$(self.contactFormSelector).valid() || self.source.get('params.invalid')) {
                    self.form().focusInvalid();
                    self._enableSubmitButton();
                    event.preventDefault();
                }
            });

            return this;
        },

        /**
         * Disable contact form submit button
         *
         * @private
         */
        _disableSubmitButton: function () {
            $(this.contactFormSelector).find(':submit').attr('disabled', 'disabled');
        },

        /**
         * Enable contact form submit button
         *
         * @private
         */
        _enableSubmitButton: function () {
            $(this.contactFormSelector).find(':submit').removeAttr('disabled');
        }
    });
});
