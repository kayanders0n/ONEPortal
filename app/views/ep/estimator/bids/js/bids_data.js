function loadBidsData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();


    var date_type  =  $('#form-bids-data #date-type option:selected').val();
    var date_start =  $('#form-bids-data #date-start').val();
    var date_end   =  $('#form-bids-data #date-end').val();
    var search_type = $('#form-bids-data #search-type option:selected').val();


    // load bids list

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/bids/list?date_type=' + date_type +
             '&date_start=' + date_start +
             '&date_end=' + date_end +
             '&search_type=' + search_type +
             '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('.table').removeClass('hidden');
            $('.loader').addClass('hidden');

            $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
            var data_table = $('#bids-data').DataTable({
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
                order: [[7, 'desc']],
                info: true,
                columnDefs: [
                    //     { visible: false, targets: [7, 8] }
                    { targets: -1, responsivePriority: 2 },
                    { targets: 0, responsivePriority: 1, width: "5%", },
                    { targets: 1, width: "10%", },
                    { targets: 2, width: "15%", },
                    { targets: 3, width: "5%", },
                    { targets: 4, width: "15%", },
                    { targets: 5, width: "5%", className: "text-center", },
                    { targets: 6, width: "5%", className: "text-right", },
                    { targets: 7, width: "6%", className: "text-center", },
                    { targets: 8, width: "6%", className: "text-center", },
                    { targets: 9, width: "7%", className: "text-center", },
                    { targets: 10, width: "21%", className: "text-left", }
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
                    //$(row).attr('style', 'cursor: pointer;');
                }
            });


            $.each(data.results, function (key, item) {

                var $item = item.item;

                var item_id = $item.item_id;
                var bid_num = $item.bid_num;
                var customer_name = $item.customer_name;
                var project_name = $item.project_name;
                var project_series = $item.project_series;
                var project_city = $item.project_city;
                var project_area = $item.project_area;
                var lot_count = $item.lot_count;
                var bid_date_due = $item.bid_date_due;
                var bid_date_sent = $item.bid_date_sent;
                var bid_date_award = $item.bid_date_award;
                var company_bid_data = '<div class="bids-info" style="display: none; margin-right: 5px;">' +
                                       $item.plumbing.bid_info +
                                       $item.concrete.bid_info +
                                       $item.framing.bid_info +
                                       $item.door_trim.bid_info +
                                       '</div>';
                company_bid_data += '<div class="bid-info" style="font-size: 0.9em;">' + $item.bid_info + '</div>';
                company_bid_data += '<button type="button" class="btn btn-sm btn-info" style="margin-left: 5px;" title="Show/Hide Bid Info" onclick="toggleBidsInfo(' + item_id + ');">...</button>';
                company_bid_data += '<button type="button" class="btn btn-sm btn-primary" style="margin-left: 5px;" title="Edit Bid Info" onclick="showBidsModal(' + item_id + ', \'edit\');">Edit</button>';
                company_bid_data += $item.bid_flag;

                var add_data = [];

                add_data.push(bid_num);
                add_data.push(customer_name);
                add_data.push(project_name);
                add_data.push(project_series);
                add_data.push(project_city);
                add_data.push(project_area);
                add_data.push(lot_count);
                add_data.push(bid_date_due);
                add_data.push(bid_date_sent);
                add_data.push(bid_date_award);
                add_data.push(company_bid_data);

                add_data.push(item_id);
                //add_data.push(row_click);

                data_table.row.add(add_data).draw();
            });

        },
        error: function (handle, status, error) {
            console.log('GetBidsDataList: ' + error + ' ' + status);
        }
    });
}

function toggleBidsInfo(item_id) {
    $('#item-' + item_id + ' .bids-info').toggle();
    $('#item-' + item_id + ' .bid-info').toggle();
}

function bidInfoAll(action) {
    if (action == 'show') {
        $('#bids-data .bids-info').show();
        $('#bids-data .bid-info').hide();
    } else if (action == 'hide') {
        $('#bids-data .bids-info').hide();
        $('#bids-data .bid-info').show();
    }
}