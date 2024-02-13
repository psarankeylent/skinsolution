define([
    'underscore',
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'mageUtils'
], function (_, $, Abstract, utils) {
    'use strict';

    return Abstract.extend({
        defaults: {
            options: [],
            stars: [],
            template: 'Aheadworks_Helpdesk2/ui/form/components/ticket/top-panel/rating-vote',
            requestUrl: '',
            ticketIdField: 'key',
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super()
                .observe('value disabled options');
        },

        /**
         * @inheritDoc
         */
        setInitialValue: function () {
            this._super();

            this.value(this.source.get('data.' + this.dataScope));
            this.value.subscribe(
                this.onValueChange, this
            );

            this._initOptions();

            return this;
        },

        /**
         * Init star options
         *
         * @private
         */
        _initOptions: function() {
            var star, i, self = this;

            _.each(this.options(), function (option) {
                star = {
                    index:  option.value,
                    id: self._getStarUid(option.value),
                    name: self.index,
                    value: option.value,
                    labelClass: self._getStarLabelClass(option.value),
                    title: option.label
                };

                self.stars.push(star);
            });
        },

        /**
         * @param index
         * @return {string}
         * @private
         */
        _getStarUid: function(index) {
            return this.index + '-' + index;
        },

        /**
         * @param index
         * @return {string}
         * @private
         */
        _getStarLabelClass: function(index) {
            return 'rating-' + index;
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
         * Handler for change value
         */
        onValueChange: function () {
            this._sendRequest();
        },

        /**
         * Send request
         *
         * @private
         */
        _sendRequest: function () {
            var key = this.source.get('data.' + this.ticketIdField);
            utils.submit({
                'url': this.requestUrl,
                'data': {
                    key: key,
                    rating: this.value()
                }
            });
        }
    });
});
