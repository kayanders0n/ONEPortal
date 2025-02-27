function loadCatalogData() {
    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var category_id = parseInt($('#category-id').val());
    var product_search = $('#product-search').val();
    var product_upc = $('#product-upc').val();

    if ((category_id !== 0) || (product_search != '') || (product_upc != '')) {

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/material/list?price_data=1&category_id=' + category_id +
                '&product_search=' + product_search +
                '&upc=' + product_upc +
                '&tick=' + Math.random(),
            success: function (data, status, handle) {

                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#catalog-data').DataTable({
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
                    order: [[1, 'asc']],
                    info: true,
                    columnDefs: [
                        { targets: 0, responsivePriority: 1, width: "10%" },
                        { targets: 1, width: "55%" },
                        { targets: 2, width: "20" },
                        { targets: 3, width: "15%" },
                        { targets: [3], className: 'text-right'},
                    ],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ records',
                        paginate: {
                            previous: '<i class="fas fa-angle-double-left"></i>',
                            next: '<i class="fas fa-angle-double-right"></i>'
                        }
                    },
                    'createdRow': function (row, data, dataIndex) {
                        $(row).attr('id', 'item-' + data[4]);
                        //$(row).attr('onClick', data[5]);
                        //$(row).attr('style', 'cursor: pointer;');
                    }
                });

                $.each(data.results, function (key, item) {

                    var add_data = [];

                    var $item = item;
                    var item_id = $item.material.item_id;

                    var details_click = 'showCatalogDetailsModal(' + item_id + '); return false;';

                    var material_code = '<span style="color: blue; cursor: pointer;" onClick="' + details_click + '">' + $item.material.code + '</span>';

                    var material_name = $item.material.name;
                    var category_name = $item.category.name;

                    var price_click = 'showCatalogPricesModal(' + item_id + '); return false;';

                    var std_price     = '<span style="font-weight: bold; font-size: 1.25em; cursor: pointer;" onClick="' + price_click + '">' + $item.catalog.std_price.formatted + '</span>';
                    if ($item.catalog.std_price.amount == 0) {
                        std_price = '<span style="color: blue; cursor: pointer;" onClick="' + price_click + '">See Details</span>';
                    }

                    add_data.push(material_code);
                    add_data.push(material_name);
                    add_data.push(category_name);
                    add_data.push(std_price);
                    add_data.push(item_id);
                    //add_data.push(row_click); // force them to click on the details if they want it

                    data_table.row.add(add_data).draw();
                });
            },
            error: function (handle, status, error) {
                $('.loader').addClass('hidden');
                alert('Failed! E:' + error + ' S: ' + status + ' D: ' + handle.responseText);
            }
        })
    } else {
        $('.loader').addClass('hidden');
    }
}


function loadCatalogCategoryList() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/material/category/list?tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#category-id').empty();
            $('#category-id').append('<option value="0" selected>-- Select Product Line --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.category;
                $('#category-id').append('<option value="' + $item.item_id + '">' + $item.name + '</option>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadCatalogCategoryList: ' + error + ' ' + status);
        }
    });
}


function clearData() {
    $('.table').addClass('hidden');
    $('.loader').addClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    $('#category-id').val('0');
    $('#product-search').val('');
    $('#product-upc').val('');
    $('#product-search').focus();

}