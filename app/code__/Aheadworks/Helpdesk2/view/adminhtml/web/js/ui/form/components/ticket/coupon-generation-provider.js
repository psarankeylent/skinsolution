define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/provider',
    'Aheadworks_Helpdesk2/js/model/backend-message-manager',
    'Aheadworks_Helpdesk2/js/action/show-alert-message',
    'Aheadworks_Helpdesk2/js/action/ticket/reload-component',
    'Aheadworks_Helpdesk2/js/action/update-data-source',
], function (_, registry, Provider, messageManager, showAlertMessage, reloadComponent, updateDataSource) {
    'use strict';

    return Provider.extend({
        defaults: {
            reloadAfterSubmit: false,
            externalSource: false
        },

        /**
         * @inheritDoc
         */
        save: function (options) {
            if (options.response.data) {
                options.response.data.subscribe(this.onResponse, this);
            }

            return this._super();
        },

        /**
         * Handler
         *
         * @param response
         */
        onResponse: function (response) {
            if (_.isObject(response)) {
                if (response.error) {
                    showAlertMessage('error', 'Request error', response.message);
                } else {
                    messageManager.addSuccessMessage(response.message, 5000);
                    if (this.reloadAfterSubmit) {
                        reloadComponent(this.reloadAfterSubmit);
                    }
                    if (this.externalSource && registry.has(this.externalSource)) {
                        updateDataSource(registry.get(this.externalSource), response.data);
                    }
                }
            }
        }
    });
});
