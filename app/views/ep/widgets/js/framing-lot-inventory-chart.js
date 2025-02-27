var FramingLotInventoryTable;

function LoadFramingLotInventoryTable() {

    $('#framing-lot-inventory-loader').removeClass('hidden');

    $('#framing-lot-inventory-data tbody').empty();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/framing/communityinfo/list?summarize=1&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#framing-lot-inventory-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                row_data = '<tr><td style="width: 61%">' + item.builder_name + '</td>' +
                    '<td style="width: 13%">' + item.total_jobsites + '</td>' +
                    '<td style="width: 13%">' + item.total_jobs + '</td>' +
                    '<td style="width: 13%">' + item.total_lots_remaining + '</td></tr>';
                $('#framing-lot-inventory-data tbody').append(row_data);

            });

        },
        error: function (handle, status, error) {
            console.log('LoadFramingLotInventoryTable: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadFramingLotInventoryTable();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
