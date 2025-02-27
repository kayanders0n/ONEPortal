function loadJobTicketsData() {
    var job_id = $('#job-id').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/show/' + job_id + '?tick=' + Math.random(),
        success: function (data, status, handle) {

            var $item = data.result;

            $('#jobsite-id').val($item.jobsite.id);
            $('#builder-name').html($item.builder.name);
            $('#project-name').html($item.project.name);
            $('#lot-num').html($item.jobsite.code);

            loadJobTickets();
        },
        error: function (handle, status, error) {
            console.log('loadJobTicketsData: ' + error + ' ' + status);
        }
    });
}


function loadJobTickets() {

    var job_id = parseInt($('#job-id').val());

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tasks/list?data_id=' + job_id + '&data_type_id=97&type_id=1084076&tick=' + Math.random(),
        success: function (data, status, handle) {

            var data_table = $('#job-tickets-data').DataTable({
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
                order: [[0, 'desc']],
                info: true,
                columnDefs: [
                    { targets: -1, responsivePriority: 2 },
                    { targets: 0, responsivePriority: 1, width: "20%", },
                    { targets: 1, width: "60%", },
                    {
                        targets: 2,
                        width: "20%",
                        createdCell: function
                            (td, cellData, rowData, row, col) {
                            $(td).attr('style', rowData[5]);
                        }
                    },
                    { targets: 3, visible: false, }
                ],
                language: {
                    info: 'Showing _START_ to _END_ of _TOTAL_ records',
                    paginate: {
                        previous: '<i class="fas fa-angle-double-left"></i>',
                        next: '<i class="fas fa-angle-double-right"></i>'
                    }
                },
                'createdRow': function (row, data, dataIndex) {
                    $(row).attr('id', 'item-' + data[3]);
                    $(row).attr('onClick', data[4]);
                    $(row).attr('style', 'cursor: pointer;');
                }
            });

            $.each(data.results, function (key, item) {

                var item_id = item.task.item_id;
                var ticket_num = item.task.num;
                var ticket_name = item.task.name;
                var completed = item.task.completed;
                var ticket_date = item.task.schedule_start;
                var date_style = '';
                if (completed == 1) {
                    ticket_date = item.task.actual_finish;
                    date_style = 'color: #cc0000';
                }

                var row_click = 'showJobTicketsModal(' + item_id + ', "edit");';

                var add_data = [];

                add_data.push(ticket_num);
                add_data.push(ticket_name);
                add_data.push(ticket_date);

                add_data.push(item_id);
                add_data.push(row_click);
                add_data.push(date_style);

                data_table.row.add(add_data).draw();

            });

        },
        error: function (handle, status, error) {
            console.log('loadJobTickets: ' + error + ' ' + status);
        }
    });

}