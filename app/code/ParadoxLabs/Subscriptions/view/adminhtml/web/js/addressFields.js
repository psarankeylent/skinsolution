/*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";

    $.widget('mage.subscriptionsAddressFields', {
        options: {
            addressSelector: '#shipping_address_id',
            fieldSelector: '.admin__field.toggle',
            inputSelector: 'select, input'
        },

        _create: function() {
            this.element.on(
                'change',
                this.options.addressSelector,
                this.toggleAddressVisibility.bind(this)
            );

            this.toggleAddressVisibility();
        },

        toggleAddressVisibility: function() {
            var fields       = this.element.find(this.options.fieldSelector);
            var isNewAddress = this.element.find(this.options.addressSelector).val() <= 0;

            if (this.element.find(this.options.addressSelector).length == 0) {
                return;
            }

            fields.toggle(isNewAddress);
            fields.find(this.options.inputSelector).prop('disabled', !isNewAddress);
        }
    });

    return $.mage.subscriptionsAddressFields;
});
