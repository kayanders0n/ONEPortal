function LoadJobQAData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear hyphen list
    $('.data-table').DataTable().clear().destroy();

    var builder_id = $('#builder-id').val();

    $.ajax({
        cache: false,
        type: 'get',
        dataType: 'json',
        url: '/job-qa/list?builder_id=' + builder_id +
            '&tick=' + Math.random(),
        success: function (data, status, handle) {

            console.log(data.results);

            $('.table').removeClass('hidden');
            $('.loader').addClass('hidden');

            $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
            var data_table = $('#job-qa-data').DataTable({
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
                order: [[5, 'desc']],
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
                    $(row).attr('id', 'item-' + data[6]);
                    $(row).attr('onClick', data[7]);
                    $(row).attr('style', 'cursor: pointer;');
                }
            });

            if (data.message === 'OK') {

                $.each(data.results, function (key, item) {

                    var add_data = [];

                    var $item = item;
                    var item_id = $item.job_qa.seq_id;
                    var qa_id = $item.job_qa.qa_id;
                    var job_id = $item.job_qa.job_id;
                    var qa_type = $item.job_qa.qa_type;
                    var is_audit = $item.job_qa.is_audit;
                    var project_code = $item.job_qa.project_code;
                    var project_name = $item.job_qa.project_name;
                    var created_on = $item.job_qa.created_on;
                    var jobsite_id = $item.jobsite.id;
                    var jobsite_address = $item.jobsite.address;
                    var doc_server_id = $item.docs.id;
                    var doc_server_filename = $item.docs.filename;

                    var type = 'Unknown';

                    if (qa_type === 1000) {
                        type = 'Trim';
                    }
                    if (qa_type === 1001) {
                        type = 'Camera';
                    }
                    if (qa_type === 1002) {
                        type = 'Top-Out';
                    }
                    if (qa_type === 1003) {
                        type = 'Gas';
                    }
                    if (qa_type === 1004) {
                        type = 'Rough-In';
                    }
                    if (qa_type === 2000) {
                        type = 'Concrete';
                    }
                    if (qa_type === 3000) {
                        type = 'Framing';
                    }

                    if (is_audit === 1) {
                        type = type + ' (Audit)';
                    }

                    var link = '<a href="https://www.thewhittonway.com/bp/jobs/getdocument.php?serverid=' + doc_server_id + '&filename=' + doc_server_filename + '" target="_blank">' + qa_id + '</a>';
                    if (doc_server_filename.toLowerCase().substr(0, 4) == 'http') { // direct URL link
                        link = '<a href="' + doc_server_filename + '" target="_blank">' + qa_id + '</a>';
                    }
                    var row_click = '';

                    add_data.push(link);
                    add_data.push(project_name);
                    add_data.push(jobsite_id);
                    add_data.push(jobsite_address);
                    add_data.push(type);
                    add_data.push(created_on);
                    add_data.push(item_id);
                    add_data.push(row_click);

                    data_table.row.add(add_data).draw();
                });
            }
        },
        error: function (handle, status, error) {
            console.log('GetJobQADataList: ' + error + ' ' + status);
        }
    })
}
