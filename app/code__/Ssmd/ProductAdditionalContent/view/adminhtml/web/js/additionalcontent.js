define([
    "jquery",
    "ko"
], function($, ko) {
    "use strict";

    return function(config) {
        $(document).on('DOMNodeInserted', function(element) {
            var e = element.target;
            if(e.nodeName == "TR") {
                $('table[data-index="product_additionalcontent"] tbody tr td:nth-child(3)').each(function(index, tr) {
                    $(this).show();
                });

                $('table[data-index="product_additionalcontent"] tbody tr td select[name$="[content_section]"]').each(function(index, tr) {

                    var name = $(this).attr('name');
                    var id = $(this).attr('id');
                    var hiddenfield = name.replace("[content_section]", "[hidden_section]");
                    var hiddenfieldelement = $(`[name='${hiddenfield}']`);

                    if ($(hiddenfieldelement).val()) {

                        $(`#${id} option[value="add-new"]`).attr("selected", "selected");

                        var inputField  = `<input className="admin__control-text" type="text" data-bind="event: {change: userChanges},value: value,hasFocus: focused,valueUpdate: valueUpdate,attr: {name: inputName,placeholder: placeholder,'aria-describedby': noticeId,id: uid,disabled: disabled,maxlength: 255}" name="${name}" aria-describedby="notice-${id}" id="${id}" maxLength="255" value="${hiddenfieldelement.val()}">`;

                        $(this).replaceWith(inputField);
                    }
                });
            }
        });

        $(document).on('change','input[name$="[content_section]"]',function(vi,indo) {
            var name = $(this).attr('name');
            var hiddenfield = name.replace("[content_section]", "[hidden_section]");

            $(`[name='${hiddenfield}']`).val($(this).val()).change();
        });

        $(document).on('change','select[name$="[content_section]"]',function(vi,indo){
            var name = $(this).attr('name');
            var id = $(this).attr('id');
            var selectedOptionChange = $(this).children("option:selected").val();
            var optionCount = $(`#${id} option`).length;
            var hiddenfield = name.replace("[content_section]", "[hidden_section]");

            if (selectedOptionChange == "add-new" || optionCount == 2 ) {

                if (optionCount == 2 ||confirm("Are you sure, you want to add a new section name")) {
                    $(`#${id} option[value="add-new"]`).attr("selected", "selected");

                    var inputField  = `<input className="admin__control-text" type="text" data-bind="event: {change: userChanges},value: value,hasFocus: focused,valueUpdate: valueUpdate,attr: {name: inputName,placeholder: placeholder,'aria-describedby': noticeId,id: uid,disabled: disabled,maxlength: 255}" name="${name}" aria-describedby="notice-${id}" id="${id}" maxLength="255">`;

                    $(this).replaceWith(inputField);

                } else {
                    $(`#${id} option[value=""]`).attr("selected", "selected");
                }
            }
        });
    }
});
