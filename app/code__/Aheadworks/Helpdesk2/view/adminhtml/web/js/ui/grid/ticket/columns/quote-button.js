define([
    'mageUtils',
    'Magento_Ui/js/grid/columns/column'
], function (utils, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            modules: {
                target: '${ $.targetElementName }'
            }
        },

        /**
         * Check if button is displayed
         *
         * @param {Object} record
         * @returns {Boolean}
         */
        isButtonDisplayed: function (record) {
            return record['type'] === 'customer-message';
        },

        /**
         * Insert quote to message area
         *
         * @param {Object} record
         */
        insertQuote: function (record) {
            var tinyEditor = window.tinyMCE.get(this.target().wysiwygId);

            tinyEditor.execCommand('mceInsertClipboardContent', false, {
                content: '<blockquote>' + record['content'] + '</blockquote><br>'
            });
            tinyEditor.focus();
        }
    });
});
