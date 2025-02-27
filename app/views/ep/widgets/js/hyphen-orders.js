function GetHyphenTotalCount() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/hyphen/list?processed=0&deleted=0&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#hyphen-waiting-total');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'white');
        },
        error: function (handle, status, error) {
            console.log('GetHyphenTotalCount: ' + error + ' ' + status);
        }
    })
}

function GetHyphenOrderCount() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/hyphen/list?type_id=1&processed=0&deleted=0&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#hyphen-waiting-orders');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'white');
            if (item_count > 20) {
                $(html_item).css('color', 'red');
            } else if (item_count > 0) {
                $(html_item).css('color', 'yellow');
            }
        },
        error: function (handle, status, error) {
            console.log('GetHyphenOrderCount: ' + error + ' ' + status);
        }
    })
}

function GetHyphenDocumentCount() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/hyphen/list?type_id=2&processed=0&deleted=0&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#hyphen-waiting-documents');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'white');
            if (item_count > 10) {
                $(html_item).css('color', 'red');
            } else if (item_count > 0) {
                $(html_item).css('color', 'yellow');
            }
        },
        error: function (handle, status, error) {
            console.log('GetHyphenDocumentCount: ' + error + ' ' + status);
        }
    })
}


$(document).ready(function () {
    GetHyphenTotalCount();
    GetHyphenOrderCount();
    GetHyphenDocumentCount();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});



