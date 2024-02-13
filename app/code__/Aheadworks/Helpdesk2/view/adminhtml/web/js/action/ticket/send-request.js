define([
    'jquery',
    'Aheadworks_Helpdesk2/js/action/send-request',
    'Aheadworks_Helpdesk2/js/action/ticket/reload-component',
    'Aheadworks_Helpdesk2/js/action/show-alert-message',
], function ($, request, reloadComponent, showAlertMessage) {
    "use strict";

    return function (url, data, reloadAfterDone) {
        var deferred = new $.Deferred();
        
        request(url, data)
            .done(function (response) {
                if (response.error) {
                    showAlertMessage('error', 'Request error', response.message);
                    deferred.reject(response);
                } else {
                    deferred.resolve(response);
                    if (reloadAfterDone) {
                        reloadComponent(reloadAfterDone);
                    }
                }
            });
        
        return deferred.promise();
    }
});
