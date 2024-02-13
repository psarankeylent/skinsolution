define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Helpdesk2/ui/grid/columns/cells/link'
        },

        /**
         * Retrieve label for column
         *
         * @returns {String}
         */
        getLabel: function(row) {
            return row[this.index + '_label'];
        },

        /**
         * Retrieve url for column
         *
         * @returns {String}
         */
        getUrl: function(row) {
            return row[this.index + '_url'];
        },

        /**
         * Overrides base method, because this component
         * can't have global field action
         *
         * @returns {Boolean}
         */
        hasFieldAction: function () {
            return false;
        }
    });
});
