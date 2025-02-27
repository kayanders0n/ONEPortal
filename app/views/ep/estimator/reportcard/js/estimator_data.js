function loadEstimatorData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var company_id = parseInt($('#company-id').val());
    var estimator_id = parseInt($('#estimator-id').val());
    var builder_id = parseInt($('#builder-id').val());

    if (company_id !== 0) {

        // load project list

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/estimator/reportcard/list?company_id=' + company_id + '&estimator_id=' + estimator_id +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {
                // load builders list
                $('#builder-id').html('');
                $('#builder-id').append('<option value="0">All</option>');
                var is_selected = '';
                $.each(data.builders, function (key, item) {
                    is_selected = '';
                    if (builder_id == item.builder.item_id) {
                      is_selected = 'selected';
                    }
                    $('#builder-id').append('<option value="' + item.builder.item_id + '" ' + is_selected + '>' + item.builder.name + '</option>');
                });


                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#estimator-data').DataTable({
                    dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    autoWidth: false,
                    responsive: true,
                    paging: true,
                    pageLength: 100,
                    processing: true,
                    searching: true,
                    ordering: true,
                    order: [[0, 'desc']],
                    info: true,
                    columnDefs: [
                        //     { visible: false, targets: [7, 8] }
                        { targets: -1, responsivePriority: 2 },
                        { targets: 0, responsivePriority: 1, width: "5%", },
                        { targets: 1, width: "26%", },
                        { targets: 2, width: "15%", },
                        { targets: 3, width: "10%", },
                        {
                            targets: 4,
                            width: "6%",
                            className: "text-center",
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].jobstats_style);
                                $(td).attr('title', rowData[13].jobstats_title);
                            },
                            orderData: 10 // sort using the hidden column
                        },
                        {
                            targets: 5,
                            width: "9%",
                            className: "text-left",
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].proposal_costdate_style);
                                $(td).attr('title', rowData[13].proposal_costdate_title);
                            }
                        },
                        {
                            targets: 6,
                            width: "10%",
                            className: "text-left",
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].proposal_contractdate_style);
                                $(td).attr('title', rowData[13].proposal_contractdate_title);
                            }
                        },
                        {
                            targets: 7,
                            width: "9%",
                            className: "text-left",
                            createdCell: function
                                (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].options_costdate_style);
                                $(td).attr('title', rowData[13].options_costdate_title);
                            }
                        },
                        {
                            targets: 8,
                            width: "5%",
                            className: "text-center",
                            createdCell: function
                                (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].billing_adj_style);
                            }
                        },
                        {
                            targets: 9,
                            width: "5%",
                            className: "text-center",
                            createdCell: function
                                (td, cellData, rowData, row, col) {
                                $(td).attr('style', rowData[13].po_review_style);
                            }
                        },
                        { // job activity
                            targets: 10,
                            visible: false,
                            dataType: 'num'
                        }
                    ],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ records',
                        paginate: {
                            previous: '<i class="fas fa-angle-double-left"></i>',
                            next: '<i class="fas fa-angle-double-right"></i>'
                        }
                    },
                    'createdRow': function (row, data, dataIndex) {
                        $(row).attr('id', 'item-' + data[11]);
                        $(row).attr('onClick', data[12]);
                        $(row).attr('style', 'cursor: pointer;');
                    }
                });

                var row = 0;
                var proposal_costdate_count = 0;
                var proposal_contractdate_count = 0;
                var options_costmargin_count = 0;
                var total_startcount = 0;
                var billing_adj_count = 0;
                var po_review_count = 0;
                //var baseproblem_percentage = 0;

                $.each(data.results, function (key, item) {

                    var $item = item.item;

                    var item_builder_id = $item.builder_id;
                    var item_estimator_id = $item.estimator_id;

                    if (builder_id != 0) {
                        if (builder_id != item_builder_id) { return; }
                    }

                    if (estimator_id != 0) {
                        if (estimator_id != item_estimator_id) { return; }
                    }

                    row++;

                    var item_id = $item.item_id;
                    var project_num = $item.project_num;
                    var project_name = $item.project_name;
                    var project_flags = $item.project_flags;
                    var project_linked = $item.project_linked;
                    var builder_name = $item.builder_name;
                    var estimator_name = $item.estimator_name;
                    var jobactivity_style = $item.jobactivity_style;
                    var jobsite_count = $item.jobsite_count;
                    var job_count = $item.job_count;
                    var startcount = $item.startcount;
                    var proposal_costdate = $item.proposal_costdate;
                    var proposal_contractdate = $item.proposal_contractdate;
                    var proposal_contractdate_flags = $item.proposal_contractdate_flags;
                    var options_costdate_flags = $item.options_costdate_flags;
                    var options_costdate = $item.options_costdate;
                    var billing_adj = $item.billing_adj;
                    var po_review_needed = $item.po_review_needed;
                    var styles_array = $item.styles_array;

                    var row_click = 'showEstimatorModal(' + item_id + ', ' + company_id + ', "edit");';

                    var add_data = [];

                    add_data.push(project_num + project_linked);
                    add_data.push(project_name + project_flags);
                    add_data.push(builder_name);
                    add_data.push(estimator_name);
                    add_data.push(jobsite_count + ' / ' + job_count + jobactivity_style);
                    add_data.push(proposal_costdate);
                    add_data.push(proposal_contractdate + proposal_contractdate_flags);
                    add_data.push(options_costdate + options_costdate_flags);
                    add_data.push(billing_adj);
                    add_data.push(po_review_needed);
                    add_data.push(startcount);

                    add_data.push(item_id);
                    add_data.push(row_click);
                    add_data.push(styles_array);

                    if (job_count > 0) {
                        if (styles_array.proposal_costdate_style !== '') {
                            proposal_costdate_count++;
                        }
                        if (styles_array.proposal_contractdate_style !== '') {
                            proposal_contractdate_count++;
                        }
                        if (options_costdate_flags !== '') {
                            options_costmargin_count++;
                        }
                    }
                    total_startcount += startcount;
                    if (billing_adj > 0) {
                        billing_adj_count++;
                    }
                    if (po_review_needed > 0) {
                        po_review_count++;
                    }
                    //baseproblem_percentage = Math.round(((proposal_contractdate_count/row)*100)*10)/10;

                    data_table.row.add(add_data).draw();
                });

                $('#total-communities').html(row);
                $('#total-startcount').html(total_startcount);
                $('#proposal-costdate-count').html(proposal_costdate_count);
                $('#proposal-contractdate-count').html(proposal_contractdate_count);
                $('#options-costmargin-count').html(options_costmargin_count);
                $('#billing-adj-count').html(billing_adj_count);
                $('#po-review-count').html(po_review_count);
                //$('#baseproblem-percentage').html(baseproblem_percentage);
            },
            error: function (handle, status, error) {
                console.log('GetEstimatorDataList: ' + error + ' ' + status);
            }
        });
    }
}

function loadEstimatorList() {
    var company_id = parseInt($('#company-id').val());
    var estimator_id = parseInt($('#estimator-id').val());

    if (company_id !== 0) {

        // load estimator drop down
        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/employee/list/list?employee_type=estimator&company_id=' + company_id +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {
                $('#estimator-id').html('');
                $('#estimator-id').append('<option value="0">All</option>');
                var is_selected = '';
                $.each(data.results, function (key, item) {
                    is_selected = '';
                    if (estimator_id == item.employee.item_id) {
                        is_selected = 'selected';
                    }
                    $('#estimator-id').append('<option value="' + item.employee.item_id + '" ' + is_selected + '>' + item.employee.name + '</option>');
                });


            },
            error: function (handle, status, error) {
                console.log('GetEmployeeList: ' + error + ' ' + status);
            }
        });
    }
}

function changeEstimator() {
    $('#builder-id').val('0');
}

function changeCompany() {
    $('#estimator-id').val('0');
    $('#builder-id').val('0');
}

function captureEstimatorTotals() {
    alert('This feature coming soon...');
}