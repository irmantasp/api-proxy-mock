import List from 'list.js';

$(function() {

    $(document).ready(function () {
        let searchOptions = {
            valueNames: [
                'name',
                'label',
                'host',
                'origin',
                'method',
                'status',
                'uri',
                'record',
            ]
        }
        new List('list-table', searchOptions);
    });
});