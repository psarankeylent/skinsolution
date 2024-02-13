define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
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
