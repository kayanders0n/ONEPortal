function loadWOItems() {

    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var wo_id = parseInt($('#wo-id').val());

    var table_content = $('#wo-item-data tbody');
    $(table_content).empty();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/wo/items/list?wo_id=' + wo_id + '&order_view=1&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('.table').removeClass('hidden');
            $('.loader').addClass('hidden');

            $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
            var data_table = $('#wo-item-data').DataTable({
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
                order: [[0, 'asc'],[1, 'asc'],],
                info: true,
                columnDefs: [
                    //     { visible: false, targets: [7, 8] }
                    { targets: 0, responsivePriority: 1, width: "15%" },
                    { targets: 1, width: "20%" },
                    { targets: 2, width: "10%" },
                    { targets: 3, width: "40%" },
                    { targets: 4, responsivePriority: 2, width: "10%" },
                    { targets: 5, width: "5%" },
                    { targets: [5], className: 'text-right'},
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

            $items = data.results;

            $.each($items, function (ikey, iitem) {
                var $item = iitem.wo_item;

                var add_data = [];

                row_click = 'showItemModal(' + $item.item_id + ', \'edit\'); return false;';

                add_data.push('<span class="text-nowrap">' + $item.location + '</span>');
                add_data.push($item.add_descript);
                add_data.push($item.material.code);
                add_data.push($item.material.name);
                add_data.push('<span class="text-nowrap" style="padding: 5px; ' + $item.wh_status.style + '">' + $item.wh_status.name + '</span>');
                add_data.push($item.units.formatted);
                add_data.push($item.item_id);
                add_data.push(row_click);

                data_table.row.add(add_data).draw();
            });


        },
        error: function (handle, status, error) {
            console.log('loadWOItems: ' + error + ' ' + status);
        }
    });

}