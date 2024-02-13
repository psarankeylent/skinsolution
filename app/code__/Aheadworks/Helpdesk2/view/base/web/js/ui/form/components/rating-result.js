define([
    'Magento_Ui/js/form/element/abstract'
], function (Text) {
    'use strict';

    return Text.extend({
        defaults: {
            worstRatingValue: 1,
            bestRatingValue: 5,
            displayIfNoRating: false,
            elementTmpl: 'Aheadworks_Helpdesk2/ui/form/element/rating-result',
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            if (this.value() < this.worstRatingValue && !this.displayIfNoRating) {
                this.visible(false);
            }

            return this;
        },

        /**
         * Get rating percent
         *
         * @return {number}
         */
        getPercent: function() {
            return Math.round(this.value() / this.bestRatingValue * 100);
        },

        /**
         * Get rating title
         *
         * @return {number}
         */
        getPercentLabel: function() {
            return this.getPercent() + '%';
        },

        /**
         * Get css width
         *
         * @return {{width: (string)}}
         */
        getStyleObject: function () {
            return {width: this.getPercentLabel()};
        }
    });
});
