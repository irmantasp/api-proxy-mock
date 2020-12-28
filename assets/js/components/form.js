import "../../scss/components/form.scss";
import CodeMirror from "codemirror/lib/codemirror";

$(function() {
    CodeMirror.fromTextArea(document.getElementById("mock_content"), {
        lineNumbers: true,
        viewportMargin: Infinity
    });
});