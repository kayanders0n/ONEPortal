function loadJobDocumentsData() {
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

            loadJobDocuments();
        },
        error: function (handle, status, error) {
            console.log('loadJobDocumentsData: ' + error + ' ' + status);
        }
    });
}


function loadJobDocuments() {

    var job_id = parseInt($('#job-id').val());
    var jobsite_id = parseInt($('#jobsite-id').val());
    var data_ids = job_id + ',' + jobsite_id;

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/document/list?data_ids=' + data_ids + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            var data_table = $('#job-documents-data').DataTable({
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
                order: [[1, 'desc']],
                info: true,
                columnDefs: [
                    { targets: -1, responsivePriority: 2 },
                    { targets: 0, responsivePriority: 1, width: "50%", },
                    { targets: 1, width: "25%", },
                    { targets: 2, width: "25%", },
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
                    //    $(row).attr('id', 'item-' + data[4]);
                    //    $(row).attr('onClick', data[5]);
                }
            });

            $.each(data.results, function (key, item) {

                var $item = item.item;
                var item_id = $item.item_id;
                var doc_name = $item.doc_name_href;
                var file_type = $item.file_type;
                var doc_date = $item.doc_date;
                var server_id = $item.server_id;

                var row_click = 'showJobDocumentModal(' + item_id + ', "edit");';

                var add_data = [];

                add_data.push(doc_name);
                add_data.push(doc_date);
                add_data.push(file_type);

                add_data.push(item_id);
                add_data.push(server_id);
                add_data.push(row_click);

                data_table.row.add(add_data).draw();

            });

        },
        error: function (handle, status, error) {
            console.log('loadJobDocuments: ' + error + ' ' + status);
        }
    });

}