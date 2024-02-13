define([
    'jquery'
], function ($) {
    "use strict";

    return function () {
        $.validator.addMethod(
            'aw_bup-validate-phone',
            function(value, element) {
                return value.match(/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){0,14}(\s*)?$/);
            },
            $.mage.__('Please specify a valid phone number')
        );

        $.validator.addMethod(
            'aw_bup-validate-image',
            function(value, element) {
                let allowedTypes = ['jpg', 'jpeg', 'gif', 'png'];

                return $.mage.isEmpty(value) || value.length < 90
                    && allowedTypes.some(function (element) {
                        return value.endsWith(element);
                    })
            },
            $.mage.__('Please choose a valid image file')
        );
    }
});