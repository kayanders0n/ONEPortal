var DBConnectionTable;

function GetDBConnectedData() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/db/connection/list?connected_on=<+CURRENT_DATE&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#db-connection-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);

            if (item_count > 0) {
                $('#db-connection-title').css('color', 'red');
            }

            if (DBConnectionTable) { DBConnectionTable.clear().destroy(); }
            DBConnectionTable = $('#db-connection-data').DataTable({
                ordering: false,
                filter: false,
                paging: false,
                info: false,
                language: {
                    zeroRecords: "No Old Connections"
                }
            });

            $.each(data.results, function (key, item) {

                var add_data = [];

                var item_id = item.conn.item_id;
                var user_name = item.conn.user_name;
                var created_on = item.conn.created_on;
                var process_name = item.conn.process_name;
                var ip_address = item.conn.ip_address;
                var employee_email = item.employee.email;

                add_data.push(item_id);
                add_data.push(ip_address);
                add_data.push(user_name);
                add_data.push(created_on);
                add_data.push(employee_email);
                add_data.push(process_name);

                DBConnectionTable.row.add(add_data).draw();
            });
        },
        error: function (handle, status, error) {
            console.log('GetDBConnectedData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    GetDBConnectedData();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});



