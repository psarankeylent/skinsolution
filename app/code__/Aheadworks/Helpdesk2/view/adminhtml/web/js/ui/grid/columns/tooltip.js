define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Helpdesk2/ui/grid/columns/cells/tooltip',
            messageAuthor: 'last_message_by',
            messageDate: 'last_message_date_formatted',
            messageType: 'last_message_type',
            messageContent: 'last_message_content'
        },

        /**
         * Retrieve message type
         *
         * @param {Object} row
         * @returns {String}
         */
        getMessageType: function(row) {
            return row[this.messageType];
        },

        /**
         * Retrieve message author
         *
         * @param {Object} row
         * @returns {String}
         */
        getMessageAuthor: function(row) {
            return row[this.messageAuthor];
        },

        /**
         * Retrieve message date
         *
         * @param {Object} row
         * @returns {String}
         */
        getMessageDate: function(row) {
            return row[this.messageDate];
        },

        /**
         * Retrieve message content
         *
         * @param {Object} row
         * @returns {String}
         */
        getMessageContent: function(row) {
            return row[this.messageContent];
        }
    });
});
