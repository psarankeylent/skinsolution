define([
    'underscore',
    'Magento_Ui/js/grid/columns/select'
], function (_, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            wrapperClass: 'colored-column',
            dataAttribute: 'data-color'
        },

        /**
         * @inheritDoc
         */
        getLabel: function (record) {
            var value = record[this.index];

            return this.wrap(value, this._super());
        },

        /**
         * Wrap value
         *
         * @private
         * @param value
         * @param label
         * @return {String}
         */
        wrap: function (value, label) {
            var template =
                _.template('<span class="<%= wrapperClass %>" <%= dataAttribute %>="<%= value %>"><%= label %></span>');

            return template({
                wrapperClass: this.wrapperClass,
                dataAttribute: this.dataAttribute,
                value: value,
                label: label
            });
        }
    });
});
