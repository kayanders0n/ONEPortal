function loadWOData() {
    var wo_id = $('#wo-id').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/wo/show/' + wo_id + '?tick=' + Math.random(),
        success: function (data, status, handle) {

            var $item = data.result;

            $('#wo-company-id').val($item.company.id);

            $('#wo-name').html($item.wo.name);
            $('#wo-status').html($item.wo.status);
            $('#wo-comment').html($item.wo.comment);
            $('#wo-date').html($item.wo.date);
            $('#wo-note').html($item.wo.note);

            $('#created-by').html($item.wo.created_by);
            $('#modified-by').html($item.wo.modified_by);

            $('#job-num').html('<a href="/jobs/' + $item.job.num + '">' + $item.job.num + '</a>');
            $('#job-name').html($item.job.community.name);
            $('#job-lot-num').html($item.job.site.code);
            $('#job-builder-name').html($item.job.builder.name);

            // set the map link for the address
            var maps = 'https://maps.google.com/?q=';
            if (navigator.userAgent.match(/(iPhone|iPad)/)) {
                maps = 'https://maps.apple.com/?q=';
            }
            maps = maps + $item.job.site.address1 + ' ' + $item.job.site.city + ', ' + $item.job.site.state;
            $('#job-address-link').html('<a href="' + maps + '" target="_blank">' + $item.job.site.address1 + '</a>');

            loadWOItems(); // needs to be here to make sure it is after the required data is loaded
        },
        error: function (handle, status, error) {
            console.log('loadWOData: ' + error + ' ' + status);
        }
    });
}

function addWorkOrderNote() {
    var wo_id = parseInt($('#add-note-form #wo-id').val());
    var wo_note = $('#add-note-form #add-note').val();

    var user_name = $('#add-note-form #user-name').val();

    $('#add-note-form #add-note').val('');

    $.ajax({
        type: 'post',
        url: '/wo/update/' + wo_id,
        data: {
            note: wo_note,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            var today = new Date();
            $('#wo-note').prepend(user_name + ' ' + today.toLocaleString('en-US') + '<br/>' + wo_note + '<br/><br/>');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function printWorkOrder() {
    var wo_id          = parseInt($('#print-wo-form #wo-id').val());
    var wo_num         = parseInt($('#print-wo-form #wo-num').val());
    var company_id     = parseInt($('#wo-company-id').val());
    var site_code      = $('#print-wo-form #user-site').val();
    var override_print = $("#print-wo-form input:radio[name ='override_location']:checked").val();
    var employee_id    = $('#print-wo-form #user-employee-id').val();
    var user_name      = $('#print-wo-form #user-name').val();

    $.ajax({
        type: 'post',
        url: '/wo/print/' + wo_id,
        data: {
            wo_num: wo_num,
            company_id: company_id,
            site_code: site_code,
            override_print: override_print,
            employee_id: employee_id,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            alert('W/O Printed');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}