define([
    'mage/template',
    'text!Aheadworks_Helpdesk2/template/ui/result-message.html',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (mageTemplate, connectionResultTmpl, alert, $t) {
    'use strict';

    return function (resultClass, title, message) {
        var data = {
            class: resultClass,
            content: $t(message)
        };

        alert({
            title: $t(title),
            content: mageTemplate(connectionResultTmpl, {'data': data})
        });
    }
});
