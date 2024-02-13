define([
    'jquery'
], function ($) {
    "use strict";

    return function (validator) {
        validator.addRule(
            'aw-helpdesk2__validate-emails',
            function (value) {
                var validRegexp, emails, i;

                if ($.mage.isEmpty(value)) {
                    return true;
                }
                validRegexp = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i; //eslint-disable-line max-len
                emails = value.split(/[\s\n\,]+/g);

                for (i = 0; i < emails.length; i++) {
                    if (!validRegexp.test(emails[i].trim())) {
                        return false;
                    }
                }

                return true;
            },
            $.mage.__('Please enter valid email addresses, separated by commas. For example, johndoe@domain.com, johnsmith@domain.com.') //eslint-disable-line max-len
        );

        /**
         * Add department select field validation
         */
        validator.addRule(
            'aw-helpdesk2__validate-department',
            function(value, param, validationParams) {
                var select = validationParams.field,
                    selectedOption = select.getOption(select.value());

                if (select.isGuestCustomer) {
                    return Boolean(selectedOption.is_allow_guest)
                }
                return true;
            },
            $.mage.__('Please, log in to submit this request type.')
        );


        return validator;
    }
});
