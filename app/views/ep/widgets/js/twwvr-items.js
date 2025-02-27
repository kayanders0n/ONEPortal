function GetTWWVRNotLinkedTotal() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/list?processed=0&deleted=0&not_linked=1&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#twwvr-not-linked-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'black');
        },
        error: function (handle, status, error) {
            console.log('GetTWWVRNotLinkedTotal: ' + error + ' ' + status);
        }
    })
}

function GetTWWVRQueueTotal() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/list?processed=0&deleted=0&field_only=1&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#twwvr-queue-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'black');
        },
        error: function (handle, status, error) {
            console.log('GetTWWVRQueueTotal: ' + error + ' ' + status);
        }
    })
}

function GetTWWVRLast24hTotal() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/list?deleted=0&field_only=1&last_24h=1&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#twwvr-last-24h-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'black');
        },
        error: function (handle, status, error) {
            console.log('GetTWWVRLast24hTotal: ' + error + ' ' + status);
        }
    })
}


$(document).ready(function () {
    GetTWWVRNotLinkedTotal();
    GetTWWVRQueueTotal();
    GetTWWVRLast24hTotal();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});



