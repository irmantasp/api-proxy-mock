import '../scss/app.scss';

import 'bootstrap/dist/js/bootstrap';
import 'bootstrap/js/dist/button';
import 'codemirror/lib/codemirror';

$(function() {

    CodeMirror.fromTextArea(document.getElementById("mock_content"), {
        lineNumbers: true,
        viewportMargin: Infinity
    });

});

