/*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";

    $.widget('mage.subscriptionsEdit', {
        options: {
            paymentSelector: '#tokenbase_id',
            paymentFormsSelector: '.payment-forms > *',
            billingFormSelector: '.billing-address',
            billingAddressSelector: '#billing_address_id',
            billingFieldSelector: '.billing-address .field:not(.do-not-toggle)',
            shippingAddressSelector: '#shipping_address_id',
            shippingFieldSelector: '.shipping-address .field:not(.do-not-toggle)',
            inputSelector: 'select, input'
        },

        _create: function() {
            // Handle payment method change
            this.element.on(
                'change',
                this.options.paymentSelector,
                this.togglePaymentVisibility.bind(this)
            );

            this.togglePaymentVisibility();

            // Handle billing address input chnages
            this.element.on(
                'change',
                this.options.billingAddressSelector,
                this.toggleBillingVisibility.bind(this)
            );

            // Initialize
            this.toggleBillingVisibility();

            // Control -- country field likes to escape
            setInterval(
                this.toggleBillingVisibility.bind(this),
                1000
            );

            // Handle shipping address input changes
            if ($(this.options.shippingAddressSelector).length > 0) {
                this.element.on(
                    'change',
                    this.options.shippingAddressSelector,
                    this.toggleShippingVisibility.bind(this)
                );

                // Initialize
                this.toggleShippingVisibility();

                // Control -- country field likes to escape
                setInterval(
                    this.toggleShippingVisibility.bind(this),
                    1000
                );
            }
        },

        togglePaymentVisibility: function() {
            var selectedMethod = this.element.find(this.options.paymentSelector).find(':selected').data('method');
            var isOffline      = this.element.find(this.options.paymentSelector).find(':selected').data('offline');

            this.element.find(this.options.paymentFormsSelector).hide();
            this.element.find('#payment_form_' + selectedMethod).show();

            this.element.find(this.options.billingFormSelector).toggle(isOffline === 1);
        },

        toggleBillingVisibility: function() {
            var fields       = this.element.find(this.options.billingFieldSelector);
            var isNewAddress = this.element.find(this.options.billingAddressSelector).val() <= 0;

            fields.toggle(isNewAddress);
            fields.find(this.options.inputSelector).prop('disabled', !isNewAddress);
        },

        toggleShippingVisibility: function() {
            var fields       = this.element.find(this.options.shippingFieldSelector);
            var isNewAddress = this.element.find(this.options.shippingAddressSelector).val() <= 0;

            fields.toggle(isNewAddress);
            fields.find(this.options.inputSelector).prop('disabled', !isNewAddress);
        }
    });

    return $.mage.subscriptionsEdit;
});
