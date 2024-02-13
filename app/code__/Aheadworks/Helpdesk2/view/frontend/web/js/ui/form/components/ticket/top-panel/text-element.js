define([
    'uiElement'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Aheadworks_Helpdesk2/ui/form/components/ticket/top-panel/text-element',
            visible: true,
            imports: {
                config: '${ $.configProvider }:data',
            }
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            this._super();
            this.observe(['visible']);

            return this;
        },

        /**
         * Prepare label
         *
         * @returns {String}
         */
        getLabel: function () {
            return this.label;
        },

        /**
         * Prepare text value
         *
         * @returns {String}
         */
        getValue: function () {
            return this.config[this.index];
        }
    });
});
