function showCatalogPricesModal(item_id) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/material/show/' + item_id + '?price_data=1&tick=' + Math.random(),
        success: function (data) {

            var $item = data.result;
            var item_id = $item.material.item_id;
            var material_code = $item.material.code;
            var material_name = $item.material.name;
            var material_upc = $item.material.upc;
            var category_name = $item.category.name;


            $('#catalog-prices-body #material-code').html(material_code);
            $('#catalog-prices-body #material-name').html(material_name);
            $('#catalog-prices-body #category-name').html(category_name);
            $('#catalog-prices-body #material-upc').html(material_upc);

            $('#form-catalog-prices #item-id').val(item_id);


            if ($item.prices != undefined) {

                $('#catalog-prices-table tbody').empty();

                $.each($item.prices, function (key, item) {

                    var $item = item.price;
                    $('#catalog-prices-table tbody').append('<tr><td>' + $item.supplier_name + '</td><td>' +
                                                                         $item.site_name + '</td><td class="text-right" style="font-weight: bold; font-size: 1.25em;">' +
                                                                         $item.supplier_price.formatted + '</td><td>' +
                                                                         $item.supplier_reference + '</td></tr>');
                });
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-catalog-prices').modal();
}

function showCatalogDetailsModal(item_id) {

    $('#form-catalog-details #add-material-upc').val('');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/material/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {

            var $item = data.result;
            var item_id = $item.material.item_id;
            var material_code = $item.material.code;
            var material_name = $item.material.name;
            var material_upc = $item.material.upc;
            var category_name = $item.category.name;

            $('#catalog-details-body #material-code').html(material_code);
            $('#catalog-details-body #material-name').html(material_name);
            $('#catalog-details-body #category-name').html(category_name);

            $('#form-catalog-details #item-id').val(item_id);
            $('#catalog-details-body #material-upc').val(material_upc);

            $('#catalog-upc-table tbody').empty();

            if ($item.material.upc != undefined) {

                $('#catalog-upc-table tbody').append('<tr><td>' + $item.material.upc + '</td></tr>>');
            }

            if ($item.upc_list != undefined) {

                $.each($item.upc_list, function (key, item) {

                    var $item = item.upc;
                    $('#catalog-upc-table tbody').append('<tr><td>' + $item.upc + '</td></tr>');

                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-catalog-details').modal();
}

function saveCatalogDetailsData() {

    var item_id = parseInt($('#form-catalog-details #item-id').val());

    var default_upc = $('#form-catalog-details #material-upc').val();
    var add_upc = $('#form-catalog-details #add-material-upc').val();

    var user_name = $('#form-catalog-details #user-name').val();

    if (add_upc.trim() == '') { return false; }

    $.ajax({
        type: 'post',
        url: '/material/update/' + item_id,
        data: {
            default_upc: default_upc,
            upc: add_upc,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            $('#form-catalog-details #add-material-upc').val('');
            $("#modal-catalog-details").modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    return false;
}

function validateUPCDataAndSave() {

    var add_upc = $('#form-catalog-details #add-material-upc').val();

    if (add_upc.trim() == '') { return false; }

    $.ajax({
        type: 'get',
        url: '/material/upc/' + add_upc,
        dataType: 'json',
        success: function (data) {
            if (data.status == 'GOOD') {
                saveCatalogDetailsData();
            } else {
                alert('Invalid UPC Code!  Must be valid 12 digit UPC!');
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    return false;

}