define([
    'underscore',
    'Magento_Ui/js/form/element/file-uploader',
    './abstract'
], function (_, FileUploader, AbstractPreview) {
    'use strict';

    //todo file uploader preview is not working
    return FileUploader.extend(AbstractPreview).extend({
        defaults: {
            service: {
                template: 'Aheadworks_Helpdesk2/ui/form/element/ticket/preview/service/button',
            }
        },

        /**
         * Save button click handler
         */
        onSaveButtonClick: function () {
            if (!_.isEmpty(this.value())
                && this.value() !== this.initialValue
            ) {
                this.save();
            }

            this.enablePreviewMode();
        }
    });
});
