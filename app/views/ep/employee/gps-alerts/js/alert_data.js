function LoadAlertData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var alert_type = $('#alert-type').val();
    var company_id = parseInt($('#company-id').val());
    var employee_num = parseInt($('#employee-num').val());
    var show_date_start = $('#show-date-start').val();
    var show_date_end = $('#show-date-end').val();
    var company_site = $('#company-site').val();
    var company_department = $('#company-department').val();


    if (alert_type) {
        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/employee/gps-alerts/list?company_id=' + company_id +
                '&employee_num=' + employee_num +
                '&alert_type=' + alert_type +
                '&date_start=' + show_date_start +
                '&date_end=' + show_date_end +
                '&company_site=' + company_site +
                '&company_department=' + company_department +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {

                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#alert-data').DataTable({
                    dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    pageLength: 25,
                    processing: true,
                    searching: false,
                    ordering: true,
                    order: [[5, 'asc']],
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
                        $(row).attr('id', 'item-' + data[7]);
                        $(row).attr('onClick', data[8]);
                        $(row).attr('style', 'cursor: pointer;');
                    }
                });

                $.each(data.results, function (key, item) {

                    var add_data = [];

                    var $alert = item.alert;
                    var $employee =  item.employee;
                    var $vehicle = item.vehicle;

                    var item_id = $alert.item_id;
                    var vehicle_num = $vehicle.num;
                    var vehicle_name = $vehicle.name;
                    var employee_num = $employee.num;
                    var employee_name = $employee.name;
                    var alert_condition = $alert.condition;
                    var alert_time = $alert.date_time;
                    var alert_lat = $alert.latitude;
                    var alert_lon = $alert.longitude;

                    loc_link = 'Not Available';
                    if (alert_lat && alert_lon) {
                        loc_link = '<a href = "https://www.google.com/maps?daddr=' + alert_lat + ',' + alert_lon + '" target="_blank">Alert Location</a>';
                    }

                    var row_click = 'showAlertModal(' + item_id + ', "view");';

                    add_data.push(vehicle_num);
                    add_data.push(vehicle_name);
                    add_data.push(employee_num);
                    add_data.push(employee_name);
                    add_data.push(alert_condition);
                    add_data.push(alert_time);
                    add_data.push(loc_link);

                    add_data.push(item_id);
                    add_data.push(row_click);

                    data_table.row.add(add_data).draw();
                });
            },
            error: function (handle, status, error) {
                console.log('GetGPSEmployeeAlertDataList: ' + error + ' ' + status);
            }
        })
    }
}
