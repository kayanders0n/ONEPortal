function LoadTWWVRData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var employee_id = parseInt($('#employee-id').val());
    var company_id = parseInt($('#company-id').val());
    var type_id = parseInt($('#type-id').val());
    var show_data = parseInt($('#show-data').val());
    var show_date = '';
    var show_site = parseInt($('#show-site').val());

    var processed = 0;
    var deleted = 0;
    var not_linked = 0;
    var field_only = 0;
    var super_only = 0;

    if (show_data !== 0) { // only care about date if processed or deleted
        show_date = $('#show-date').val();
    }
    if (show_data === 1) { // processed only
        processed = 1;
        deleted = 0;
    } else if (show_data === 2) { // deleted only
        processed = 0;
        deleted = 1;
    } else if (show_data === 3) { // not linked
        processed = 0;
        deleted = 0;
        not_linked = 1;
    }

    if (type_id === 1) { // super only
        field_only = 0;
        super_only = 1;
    } else if (type_id === 2) { // field only
        field_only = 1;
        super_only = 0;
    }

    if (type_id !== 0) {

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/twwvr/list?employee_id=' + employee_id +
                '&company_id=' + company_id +
                '&field_only=' + field_only +
                '&super_only=' + super_only +
                '&processed=' + processed +
                '&deleted=' + deleted +
                '&not_linked=' + not_linked +
                '&date=' + show_date +
                '&site=' + show_site +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {

                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#twwvr-data').DataTable({
                    dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    pageLength: 25,
                    processing: true,
                    searching: true,
                    ordering: true,
                    order: [[6, 'asc']],
                    info: true,
                    columnDefs: [
                        //     { visible: false, targets: [7, 8] }
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 2, targets: -1 }
                    ],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ records',
                        paginate: {
                            previous: '<i class="fas fa-angle-double-left"></i>',
                            next: '<i class="fas fa-angle-double-right"></i>'
                        }
                    },
                    'createdRow': function (row, data, dataIndex) {
                        $(row).attr('id', 'item-' + data[9]);
                        //$(row).attr('onClick', data[9]);
                        //$(row).attr('style', 'cursor: pointer;');
                    }
                });

                $.each(data.results, function (key, item) {

                    var add_data = [];

                    var $item = item;
                    var item_id = $item.twwvr.item_id;
                    var record_type = $item.twwvr.record_type;
                    var activity_date = $item.twwvr.activity_date;
                    var employee_name = $item.employee.name;
                    var assigned_name = $item.assigned_employee.name;
                    var task_num = $item.task.user_num;
                    var job_num = $item.job.user_num;
                    var recorded_on = $item.file.created_on;
                    var gps_data = '<a href="http://maps.google.com/maps?q=' + $item.twwvr.latitude + ',' + $item.twwvr.longitude + '" target="_blank">GPS</a>';
                    var is_processed = $item.twwvr.processed;
                    var is_deleted = $item.twwvr.deleted;

                    var detail_click = '';
                    var process_click = '';
                    if ((is_deleted === 0) && (is_processed === 0)) {
                        detail_click = 'showTWWVRModal(' + item_id + ', \'edit\'); return false;';
                    } else {
                        detail_click = 'showTWWVRModal(' + item_id + ', \'view\'); return false;';
                    }

                    process_click = 'showTWWVRModal(' + item_id + ', \'process\'); return false;';

                    var option_data = '';
                    option_data += '<button class="btn btn-warning" type="button" onclick="' + detail_click + '" >Detail</button>';
                    option_data += '&nbsp;&nbsp;&nbsp;';
                    option_data += '<button class="btn btn-primary" type="button" onclick="' + process_click + '" >Process</button>';
                    option_data += '&nbsp;&nbsp;&nbsp;';

                    var file_name = $item.file.name;
                    var file_ext = file_name.substr( (file_name.lastIndexOf('.') +1) );
                    file_ext = file_ext.toUpperCase();
                    if ((file_ext == 'JPG') || (file_ext == 'PNG')) {
                        option_data += '<img src="/assets/images/icons/paint24.gif" title="Still Shot" align="center" />';
                    }

                    add_data.push(record_type);
                    add_data.push(activity_date);
                    add_data.push(employee_name);
                    add_data.push(task_num);
                    add_data.push(job_num);
                    add_data.push(option_data);
                    add_data.push(recorded_on);
                    add_data.push(gps_data);
                    add_data.push(assigned_name);
                    add_data.push(item_id);
                    //add_data.push(row_click);

                    data_table.row.add(add_data).draw();
                });
            },
            error: function (handle, status, error) {
                console.log('GetTWWVRDataList: ' + error + ' ' + status);
            }
        })
    }
}

function LoadEmployeeList() {

    var user_employee_id = parseInt($('#form-twwvr-data #user-employee-id').val());

    if (user_employee_id !== 0) {

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/employee/link/list?employee_id=' + user_employee_id +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {
                $.each(data.results, function (key, item) {
                    var $item = item.employee;
                    $('#employee-id').append('<option value="' + $item.id + '">' + $item.name + '</option>');

                });
            },
            error: function (handle, status, error) {
                console.log('GetEmployeeLinkList: ' + error + ' ' + status);
            }
        })
    }
}

