define(['jquery'], function ($) {
    "use strict";
    return function(config) {

        // This is not working - Replace it with UIRegistry
        $(document).on('change','input[name$="[content_section]"]',function(vi,indo) {
            console.log("Changing It here");

            var name = $(this).attr('name');
            var id = $(this).attr('id');

            var hiddenfield = name.replace("[content_section]", "[hidden_section]");

            $(`[name='${hiddenfield}']`).val($(this).val());
        });

        $(document).on('change','select[name$="[content_section]"]',function(vi,indo){
            console.log("Changing It hereeeee");

            var name = $(this).attr('name');
            var id = $(this).attr('id');

            var selectedOptionChange = $(this).children("option:selected").val();

            if (selectedOptionChange == "add-new" ) {

                if (confirm("Are you sure, you want to add a new section name")) {
                    var inputField  = `<input className="admin__control-text" type="text" data-bind="event: {change: userChanges},value: value,hasFocus: focused,valueUpdate: valueUpdate,attr: {name: inputName,placeholder: placeholder,'aria-describedby': noticeId,id: uid,disabled: disabled,maxlength: 255}" name="${name}" aria-describedby="notice-${id}" id="${id}" maxLength="255">`;

                    console.log(inputField);
                    $(this).replaceWith(inputField);

                } else {
                    $(`#${id} option[value=""]`).attr("selected", "selected");
                }
            }
        });
    }
});
