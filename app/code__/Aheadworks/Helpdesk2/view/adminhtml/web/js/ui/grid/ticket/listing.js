define([
    'Magento_Ui/js/grid/listing'
], function (Listing) {
    'use strict';

    return Listing.extend({

        /**
         * Prepare row class
         *
         * @returns {String}
         */
        getRowClass: function (row, index) {
            var classList = [];

            classList.push(row['css-row-class']);
            if (index % 2) {
                classList.push('_odd-row');
            }

            return classList.join(' ');
        }
    });
});
