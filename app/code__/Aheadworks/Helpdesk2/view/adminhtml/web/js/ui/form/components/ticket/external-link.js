define([
    './link'
], function (Link) {
    'use strict';

    return Link.extend({

        /**
         * Copy Url to clipboard
         */
        copyUrl: function () {
            var tempInput = document.createElement("input");

            tempInput.value = this.getUrl();
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            document.body.removeChild(tempInput);

            alert('Copied');
        }
    });
});
