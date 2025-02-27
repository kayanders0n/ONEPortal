function loadInventoryPOs() {
    var company_id = $('#inventory-company-id').val();

    $('#inventory-po-id').html('');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/po/list?company_id=' + company_id + '&type=2&reference=INVENTORY&completed=0&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#inventory-po-id').append('<option value="0">-- Select P/O --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.po;
                $('#inventory-po-id').append('<option value="' + $item.item_id + '">' + $item.name + '</option>');

            });

        },
        error: function (handle, status, error) {
            console.log('loadInventoryPOs: ' + error + ' ' + status);
        }
    });
}

function loadInventoryPOItems() {
    $('.table').addClass('hidden');
    $('.loader').removeClass('hidden');

    // clear table
    $('.data-table').DataTable().clear().destroy();

    var po_id = parseInt($('#inventory-po-id').val());

    if (po_id !== 0) {

        $.ajax({
            cache: false,
            type: 'GET',
            dataType: 'json',
            url: '/po/items/list?po_id=' + po_id + '&tick=' + Math.random(),
            success: function (data, status, handle) {

                $('.table').removeClass('hidden');
                $('.loader').addClass('hidden');

                $.fn.dataTable.moment('MM/DD/YYYY hh:mm:ss a','en-US');
                var data_table = $('#inventory-data').DataTable({
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
                        { targets: 0, responsivePriority: 1, width: "10%" },
                        { targets: 1, width: "35%" },
                        { targets: 2, width: "10" },
                        { targets: 3, width: "25%" },
                        { targets: 4, width: "20%" },
                        { targets: [2], className: 'text-right'},
                    ],
                    language: {
                        info: 'Showing _START_ to _END_ of _TOTAL_ records',
                        paginate: {
                            previous: '<i class="fas fa-angle-double-left"></i>',
                            next: '<i class="fas fa-angle-double-right"></i>'
                        }
                    },
                    'createdRow': function (row, data, dataIndex) {
                        $(row).attr('id', 'item-' + data[5]);
                        $(row).attr('onClick', data[6]);
                        $(row).attr('style', 'cursor: pointer;');
                    }
                });

                $.each(data.results, function (key, item) {

                    var add_data = [];

                    var $item = item.po_item;

                    var item_id = $item.item_id;

                    var add_data = [];

                    add_data.push($item.material.code);
                    add_data.push($item.material.name);
                    add_data.push($item.units_ordered.formatted);
                    add_data.push($item.location);
                    add_data.push($item.add_descript);
                    add_data.push($item.item_id);
                    add_data.push('showInventoryPORModal(' + item_id + ', "edit"' + ');');

                    data_table.row.add(add_data).draw();
                });
            },
            error: function (handle, status, error) {
                console.log('loadInventoryPOItems: ' + error + ' ' + status);
            }
        })
    }
}


function findMaterial() {
    var material_code = $('#form-inventory-material #material-code').val();
    var material_upc = $('#form-inventory-material #material-upc').val();

    if ((material_code.trim() == '') && (material_upc.trim() == '')) {
        return false;
    }

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/material/list?code=' + material_code + '&upc=' + material_upc + '&tick=' + Math.random(),
        success: function (data, status, handle) {


            $('#form-inventory-material #material-name').html('');
            $('#form-inventory-material #material-id').val(0);
            $('#inventory-count-entry').hide();

            if ((data.num_results == undefined) || (data.num_results > 1)) {
                alert('No exact match!');
                return false;
            }

            var $item = data.results[0]; // only one item so use the first one

            $('#form-inventory-material #material-code').val('');
            $('#form-inventory-material #material-upc').val('');

            $('#form-inventory-material #material-id').val($item.material.item_id);
            $('#form-inventory-material #material-name').html($item.material.name);
            $('#inventory-count-entry').show();
            $('#inventory-count-entry #inventory-count').val('');
            $('#inventory-count-entry #inventory-location').val('');
            $('#inventory-count-entry #inventory-add-descript').val('');
            $('#inventory-count-entry #inventory-count').focus();

        },
        error: function (handle, status, error) {
            console.log('findMaterial: ' + error + ' ' + status);
        }
    });

    return false;
}

function clearMaterial() {
    $('#form-inventory-material #material-code').val('');
    $('#form-inventory-material #material-upc').val('');

    $('#form-inventory-material #material-name').html('');
    $('#form-inventory-material #material-id').val(0);
    $('#inventory-count-entry').hide();

    $('#form-inventory-material #material-upc').focus();
}


function addInventoryCount(units) {
    var value = $('#inventory-count-entry #inventory-count').val();
    value = value + String(units);

    if (units == -1) { value = ''; }
    $('#inventory-count-entry #inventory-count').val(value);

    return false;
}

function addInventory() {

    var po_id           = $('#inventory-po-id').val();
    var company_id      = $('#inventory-company-id').val();
    var material_id     = $('#form-inventory-material #material-id').val();
    var inventory_count = parseFloat($('#form-inventory-material #inventory-count').val());
    var location        = $('#form-inventory-material #inventory-location').val();
    var add_descript    = $('#form-inventory-material #inventory-add-descript').val();
    var user_name       = $('#form-inventory-material #user-name').val();

    if (po_id == 0) { alert('Must select PO# from list'); return false; }
    if ((inventory_count == 0) || (inventory_count == undefined)) { alert('Must enter units on hand'); return false; }

    $.ajax({
        cache: false,
        type: 'POST',
        dataType: 'json',
        url: '/po/item/add/' + po_id,
        data: {
            company_id: company_id,
            material_id: material_id,
            order_units: inventory_count,
            location: location,
            add_descript: add_descript,
            user_name: user_name,
            tick: Math.random()
        },
        success: function (data, status, handle) {
            $('#form-inventory-material #material-name').html('');
            $('#form-inventory-material #material-id').val(0);
            $('#inventory-count-entry').hide();
            $('#form-inventory-material #material-upc').focus();

            loadInventoryPOItems();
        },
        error: function (handle, status, error) {
            console.log('loadInventoryPOs: ' + error + ' ' + status);
        }
    });

}
