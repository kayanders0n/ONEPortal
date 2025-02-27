function loadFleetData() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var company_id = parseInt($('#company-id').val());

    // load fleet list

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/fleet/list?company_id=' + company_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('.table').removeClass('hidden');
            $('.loader').addClass('hidden');

            $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
            var data_table = $('#fleet-data').DataTable({
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

            $.each(data.results, function (key, item) {

                if (company_id != 0) {
                    if (company_id != item_company_id) { return; }
                }

                var item_id = item.material.item_id;
                var asset_idcode = item.asset.idcode;
                var parked = item.asset.parked;
                var material_name = item.material.name;
                var engine_size = item.asset.engine_size;
                var configuration = item.asset.configuration;
                var weight = item.asset.vehicleweight;
                var license = item.asset.license;
                var expires = item.asset.regexpiration;
                var last_odometer = item.asset.last_odometer;
                var last_fuel_date = item.asset.last_fuel_date;
                var last_oilchange = item.asset.last_oilchange;
                var driver_name = item.driver.name;
                var location = item.location.name;
                var company = item.company.name;
                var has_gps = item.asset.has_gps;

                var row_click = 'showFleetModal(' + item_id + ', ' + company_id + ', "edit");';

                var add_data = [];

                add_data.push(asset_idcode + ' ' + parked);
                add_data.push(material_name + ' (' + engine_size + ')');
                add_data.push(configuration + weight);
                add_data.push(license + expires);
                add_data.push(last_odometer);
                add_data.push(last_fuel_date);
                add_data.push(last_oilchange);
                add_data.push(driver_name);
                add_data.push(location);
                add_data.push(company);
                add_data.push(has_gps);

                add_data.push(item_id);
                add_data.push(row_click);
                //add_data.push(styles_array);

                data_table.row.add(add_data).draw();
            });

        },
        error: function (handle, status, error) {
            console.log('GetFleetDataList: ' + error + ' ' + status);
        }
    });
}