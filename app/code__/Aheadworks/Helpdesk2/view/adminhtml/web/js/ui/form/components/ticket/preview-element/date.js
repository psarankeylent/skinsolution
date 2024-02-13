define([
    'underscore',
    'Magento_Ui/js/form/element/date',
    './abstract'
], function (_, Date, AbstractPreview) {
    'use strict';

    return Date.extend(AbstractPreview).extend({
        defaults: {
            service: {
                template: 'Aheadworks_Helpdesk2/ui/form/element/ticket/preview/service/button',
            }
        },

        /**
         * @inheritdoc
         */
        setInitialValue: function () {
            this._super();
            this.prevValue = this.value();

            return this;
        },

        /**
         * Save button click handler
         */
        onSaveButtonClick: function () {
            var value = this.value();

            if (this.validate().valid && value !== null && value !== this.prevValue) {
                this.save();
            }
            this.prevValue = value;

            this.enablePreviewMode();
        }
    });
});
