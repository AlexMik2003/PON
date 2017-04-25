$(document).ready(function () {

    var uri = location.href;

    var table = $('#raisecom').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": uri+"/json",

        },
        "columns": [
            {"data": "gpon"},
            {"data": "sn"},
            {"data": "status"},
            {"data": "id"},
        ],

        'columnDefs': [{
            'targets': 3,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta) {
                return '<a href="delete/'+data+'" class="btn btn-danger navbar-btn" role="button" id="act_dev_btn">Delete</a>';

            }
        },

            {
                'targets': [1],
                'searchable': true,
                'orderable': true,
            },
            {
                'targets': [0],
                'searchable': false,
                'orderable': true,
            },
            {
                'targets': [2],
                'searchable': false,
                'orderable': false,
            },
        ],

        "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search:",
            "paginate": {
                "previous": "Previous page",
                "next": "Next page"
            }
        }

    });

    setInterval(function () {
        table.ajax.reload(null, false);
    }, 300000);

});
