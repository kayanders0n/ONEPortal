function GetPLSJobSiteExpiredPublishNum() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/job-starts/list?expired_publish=1&count_only=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            var html_item = $('#job-starts-expired-publish-num');
            var item_count = data.num_results;
            $(html_item).html(item_count);
            $(html_item).css('color', 'white');
            if (item_count > 5) {
                $(html_item).css('color', 'red');
            } else if (item_count > 0) {
                $(html_item).css('color', 'yellow');
            }
        },
        error: function (handle, status, error) {
            console.log('GetPLSJobSiteExpiredPublishNum: ' + error + ' ' + status);
        }
    })
}


function GetPDOCFilesNum() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/pdoc-files/list?tick=' + Math.random(),
        success: function (data, status, handle) {
            var file_count = data.results[0].pdoc.old_other_file_count;
            var html_item = $('#pdoc-file-other-num');
            $(html_item).html(file_count);
            $(html_item).css('color', 'white');
            if (file_count > 5) {
                $(html_item).css('color', 'red');
            } else if (file_count > 0) {
                $(html_item).css('color', 'yellow');
            }

            var file_count = data.results[0].pdoc.old_payroll_file_count;
            var html_item = $('#pdoc-file-payroll-num');
            $(html_item).html(file_count);
            $(html_item).css('color', 'white');
            if (file_count > 5) {
                $(html_item).css('color', 'red');
            } else if (file_count > 0) {
                $(html_item).css('color', 'yellow');
            }

            var file_count = data.results[0].pdoc.old_payroll_index_file_count;
            var html_item = $('#pdoc-file-payroll-index-num');
            $(html_item).html(file_count);
            $(html_item).css('color', 'white');
            if (file_count > 5) {
                $(html_item).css('color', 'red');
            } else if (file_count > 0) {
                $(html_item).css('color', 'yellow');
            }

            var file_count = data.results[0].pdoc.old_payroll_review_file_count;
            var html_item = $('#pdoc-file-payroll-review-num');
            $(html_item).html(file_count);
            $(html_item).css('color', 'white');
            if (file_count > 20) {
                $(html_item).css('color', 'red');
            } else if (file_count > 0) {
                $(html_item).css('color', 'yellow');
            }

        },
        error: function (handle, status, error) {
            console.log('GetPDOCFilesNum: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    GetPLSJobSiteExpiredPublishNum();
    GetPDOCFilesNum();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});



