define([
   'Magento_Ui/js/form/components/button'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            urlToRedirect: ''
        },

        /**
         * Performs configured actions
         */
        action: function () {
            window.location.href = this.urlToRedirect;
        }
    });
});
