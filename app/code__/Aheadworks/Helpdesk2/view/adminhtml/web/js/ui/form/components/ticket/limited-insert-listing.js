define([
    'Magento_Ui/js/form/components/insert-listing',
], function (InsertListing) {
    'use strict';

    return InsertListing.extend({
        defaults: {
            frame: 1
        },

        /**
         * Load more items
         */
        loadMore: function() {
            this.frame += 1;
            this.externalSource().set('params.frame', this.frame);
            this.reload();
        }
    });
});
