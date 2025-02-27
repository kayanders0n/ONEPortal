function GetDBReplicationTotal() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/db/replication/list?count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#db-replication-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'black');

            // error
            var html_item = $('#db-replication-error');
            var item_count = data.num_results_error;
            $(html_item).html(item_count);
            $(html_item).css('color', 'black');

            if (item_count > 0) { $(html_item).css('color', 'Red'); }
        },
        error: function (handle, status, error) {
            console.log('GetReplicationTotal: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    GetDBReplicationTotal();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});
