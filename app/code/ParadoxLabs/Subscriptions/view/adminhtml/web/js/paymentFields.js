/*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";

    $.widget('mage.subscriptionsPaymentFields', {
        options: {
            paymentSelector: '#payment_tokenbase_id',
            paymentFormsSelector: 'fieldset.payment-method',
            offlineMethods: [],
        },

        _create: function() {
            this.element.on(
                'change',
                this.options.paymentSelector,
                this.togglePaymentVisibility.bind(this)
            );

            this.togglePaymentVisibility();
        },

        togglePaymentVisibility: function() {
            var selectedMethod = this.element.find(this.options.paymentSelector).val();
            var isOffline      = this.options.offlineMethods.indexOf(selectedMethod) >= 0;

            this.element.find(this.options.paymentFormsSelector).hide();
            this.element.find('#payment_form_' + selectedMethod).show();

            this.element.find('.field-billing_address_display').toggle(!isOffline);
            this.element.find('#subscription_fieldset_billing').toggle(isOffline);

            this.element.find('.admin__field:hidden').prop('disabled', true);
            this.element.find('.admin__field:visible').prop('disabled', false);
        },
    });

    return $.mage.subscriptionsPaymentFields;
});
