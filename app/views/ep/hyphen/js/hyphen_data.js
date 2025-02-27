function LoadHyphenData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var type_id = parseInt($('#type-id').val());
    var show_data = parseInt($('#show-data').val());
    var show_date = '';

    var processed = 0;
    var deleted = 0;

    if (show_data !== 0) { // only care about date if processed or deleted
        show_date = $('#show-date').val();
    }
    if (show_data === 1) { // processed only
        processed = 1;
        deleted = 0;
    } else if (show_data === 2) { // deleted only
        processed = 0;
        deleted = 1;
    }

    if (type_id !== 0) {

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/hyphen/list?type_id=' + type_id +
                '&processed=' + processed +
                '&deleted=' + deleted +
                '&date=' + show_date +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {

                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#hyphen-data').DataTable({
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
                    order: [[6, 'desc']],
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

                    var $item = item.hyphen;
                    var item_id = $item.item_id;
                    var project_code = $item.project_code;
                    var project_name = $item.project_name;
                    var jobsite_address = $item.jobsite_address;
                    var jobsite_lotnum = $item.jobsite_lotnum;
                    var action_name = $item.action_name;
                    var action_id = $item.action_id;
                    var action_date = $item.action_date;
                    var is_processed = $item.processed;
                    var is_deleted = $item.deleted;
                    var deleted_by = $item.deleted_by;
                    var modified_on = $item.modified_on;
                    var modified_by = $item.modified_by;

                    var row_click = '';
                    if ((is_deleted === 0) && (is_processed === 0)) {
                        row_click = 'showHyphenModal(' + item_id + ', "edit");';
                    } else {
                        row_click = 'showHyphenModal(' + item_id + ', "view");';
                    }

                    if ((is_processed) && (type_id === 1)) {
                        action_name = action_name + ' <strong>(' + action_id + ')</strong>';
                    } else if ((is_deleted) && (type_id === 1)) {
                        action_name = action_name + ' <strong>(' + deleted_by + ')</strong>';
                    }

                    add_data.push(project_code);
                    add_data.push(project_name);
                    add_data.push(action_name);
                    add_data.push(jobsite_address);
                    add_data.push(jobsite_lotnum);
                    add_data.push(action_date);
                    add_data.push(modified_on);
                    add_data.push(item_id);
                    add_data.push(row_click);

                    data_table.row.add(add_data).draw();
                });
            },
            error: function (handle, status, error) {
                console.log('GetHyphenDataList: ' + error + ' ' + status);
            }
        })
    }
}

function ReProcessHyphen() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/hyphen/reprocess?tick=' + Math.random(),
        success: function (data, status, handle) {
            console.log(data);
            LoadHyphenData();
        },
        error: function (handle, status, error) {
            console.log('ReProcessHyphen: ' + error + ' ' + status);
        }
    })
}
