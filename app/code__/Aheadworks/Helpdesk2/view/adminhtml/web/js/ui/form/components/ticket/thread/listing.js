define([
    'underscore',
    'Magento_Ui/js/grid/listing',
    'mage/apply/main'
], function (_, Listing, mage) {
    'use strict';

    return Listing.extend({
        defaults: {
            listTemplate: 'Aheadworks_Helpdesk2/ui/form/element/ticket/thread/listing-divided-by-tabs',
            tabs: []
        },

        /**
         * Retrieve tab label
         *
         * @param {Object} tab
         * @return {string}
         */
        getTabLabel: function(tab) {
            var count,
                label = tab.label;

            if (tab.showCountInLabel) {
                count = this.getRows(tab.messageType).length;
                label += ' (' + count + ')';
            }

            return label;
        },

        /**
         * Retrieve listing rows filtered by message type
         *
         * @param messageType
         * @return {Array}
         */
        getRows: function (messageType) {
            var rows = this.rows;

            if (messageType) {
                rows = _.filter(rows, function (row) {
                    if (_.isString(messageType)) {
                        return row.type === messageType
                    } else if (_.isArray(messageType)) {
                        return _.contains(messageType, row.type)
                    }
                    return true;
                });
            }

            return rows;
        },

        /**
         * @inheritDoc
         */
        onDataReloaded: function () {
            this._super();

            mage.apply();
        }
    });
});
