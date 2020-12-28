import "../../scss/components/form.scss";
import CodeMirror from "codemirror/lib/codemirror";

$(function() {
    $('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
    });

    let mockContent = document.getElementById("mock_content");
    if (mockContent) {
        CodeMirror.fromTextArea(mockContent, {
            lineNumbers: true,
            viewportMargin: Infinity
        });
    }

    let originMockContent = document.getElementById("origin_mock_content");
    if (originMockContent) {
        CodeMirror.fromTextArea(originMockContent, {
            lineNumbers: true,
            viewportMargin: Infinity
        });
    }
});