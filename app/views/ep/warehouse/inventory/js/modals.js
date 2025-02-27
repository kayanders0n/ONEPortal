function showInventoryPORModal(item_id, modal_type) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/po/item/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {

            var $item = data.result;
            var item_id      = $item.po_item.item_id;
            var material_code = $item.po_item.material.code;
            var material_name = $item.po_item.material.name;

            var inventory_count = $item.po_item.units_ordered.amount;

            var add_descript = $item.po_item.add_descript;
            var location     = $item.po_item.location;


            var created_on = $item.po_item.created_on;
            var modified_on = $item.po_item.modified_on;
            var modified_by = $item.po_item.modified_by;

            $('#inventory-po-' + modal_type + '-body #material-code').html(material_code);
            $('#inventory-po-' + modal_type + '-body #material-name').html(material_name);

            $('#inventory-po-' + modal_type + '-body #created-on').html(created_on);
            $('#inventory-po-' + modal_type + '-body #modified-on').html(modified_on);
            $('#inventory-po-' + modal_type + '-body #modified-by').html(modified_by);

            $('#form-inventory-po-' + modal_type + ' #item-id').val(item_id);
            $('#form-inventory-po-' + modal_type + ' #inventory-count').val(inventory_count);
            $('#form-inventory-po-' + modal_type + ' #location').val(location);
            $('#form-inventory-po-' + modal_type + ' #add-descript').val(add_descript);


        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-inventory-po-' + modal_type).modal();
}

function saveInventoryPOData() {
    var item_id = parseInt($('#form-inventory-po-edit #item-id').val());

    var inventory_count = parseFloat($('#form-inventory-po-edit #inventory-count').val());
    var location = $('#form-inventory-po-edit #location').val();
    var add_descript = $('#form-inventory-po-edit #add-descript').val();

    var user_name = $('#form-inventory-po-edit #user-name').val();

    $.ajax({
        type: 'post',
        url: '/po/item/update/' + item_id,
        data: {
            order_units: inventory_count,
            location: location,
            add_descript: add_descript,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {

            var update_table = $('#inventory-data').DataTable();
            var update_row = $('#item-' + item_id);
            var update_data = update_table.row(update_row).data();

            update_data[2] = inventory_count.toFixed(2);
            update_data[3] = location;
            update_data[4] = add_descript;

            update_table.row(update_row).data(update_data).draw();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function deleteInventoryPOData(modal_type) {
    var item_id = parseInt($('#form-inventory-po-' + modal_type + ' #item-id').val());

    if (confirm('Are you sure?')) {
        $.ajax({
            type: 'post',
            url: '/po/item/remove/' + item_id,
            data: {
                tick: Math.random()
            },
            dataType: 'json',
            success: function (data) {
                $('#item-' + item_id).slideUp('slow', function () {
                    $(this).remove()
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + ': ' + errorThrown);
            }
        });
    }
}

function addInventoryPODataCount(units) {
    var value = $('#form-inventory-po-edit #inventory-count').val();
    value = value + String(units);

    if (units == -1) { value = ''; }
    $('#form-inventory-po-edit #inventory-count').val(value);

    return false;
}



