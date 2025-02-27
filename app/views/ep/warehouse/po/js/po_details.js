function loadPOItems() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var po_id = parseInt($('#po-id').val());

    var table_content = $('#po-item-data tbody');
    $(table_content).empty();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/po/items/list?po_id=' + po_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('.table').removeClass('hidden');
            $('.loader').addClass('hidden');

            $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
            var data_table = $('#po-item-data').DataTable({
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
                order: [[0, 'asc']],
                info: true,
                columnDefs: [
                    //     { visible: false, targets: [7, 8] }
                    { targets: 0, responsivePriority: 1, width: "10%" },
                    { targets: 1, width: "35%" },
                    { targets: 2, width: "10" },
                    { targets: 3, width: "20%" },
                    { targets: 4, width: "15%" },
                    { targets: 5, responsivePriority: 2, width: "5%" },
                    { targets: 6, width: "5%" },
                    { targets: [5,6], className: 'text-right'},
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
                    //$(row).attr('onClick', data[4]);
                    //$(row).attr('style', 'cursor: pointer;');
                }
            });

            $items = data.results;

            $.each($items, function (ikey, iitem) {
                var $item = iitem.po_item;

                var add_data = [];

                add_data.push($item.material.code);
                add_data.push($item.material.name);
                add_data.push($item.job.num);
                add_data.push($item.add_descript);
                add_data.push($item.location);
                add_data.push($item.units_ordered.formatted);
                add_data.push($item.units_received.formatted);
                add_data.push($item.item_id);

                data_table.row.add(add_data).draw();
            });


        },
        error: function (handle, status, error) {
            console.log('loadPOItems: ' + error + ' ' + status);
        }
    });

}