function loadPOData() {
    var po_id = parseInt($('#po-id').val()) || 0;

    if (po_id == 0) {
        console.log('No PO...');
        return;
    }

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/po/show/' + po_id + '?tick=' + Math.random(),
        success: function (data, status, handle) {

            var $item = data.result;

            $('#po-company-id').val($item.company.id);

            $('#vendor-name').html($item.vendor.name);
            $('#po-name').html($item.po.name);
            $('#po-type').html($item.po.type);
            $('#po-status').html($item.po.status);
            $('#po-comment').html($item.po.comment);
            $('#po-date').html($item.po.date);
            $('#po-ship-to').html($item.shipto.name);

            $('#po-note').html($item.po.note);

            $('#created_by').html($item.po.created_by);

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

            loadPOItems(); // needs to be here to make sure it is after the required data is loaded
        },
        error: function (handle, status, error) {
            console.log('loadPOData: ' + error + ' ' + status);
        }
    });
}

function addPurchaseOrderNote() {
    var po_id = parseInt($('#add-note-form #po-id').val());
    var po_note = $('#add-note-form #add-note').val();

    var user_name = $('#add-note-form #user-name').val();

    $('#add-note-form #add-note').val('');

    $.ajax({
        type: 'post',
        url: '/po/update/' + po_id,
        data: {
            note: po_note,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            var today = new Date();
            $('#po-note').prepend(user_name + ' ' + today.toLocaleString('en-US') + '<br/>' + po_note + '<br/><br/>');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function printPurchaseOrder() {
    var po_id          = parseInt($('#print-po-form #po-id').val());
    var po_num         = parseInt($('#print-po-form #po-num').val());
    var company_id     = parseInt($('#po-company-id').val());
    var site_code      = $('#print-po-form #user-site').val();
    var override_print = $("#print-po-form input:radio[name ='override_location']:checked").val();
    var employee_id    = $('#print-po-form #user-employee-id').val();
    var user_name      = $('#print-po-form #user-name').val();

    $.ajax({
        type: 'post',
        url: '/po/print/' + po_id,
        data: {
            po_num: po_num,
            company_id: company_id,
            site_code: site_code,
            override_print: override_print,
            employee_id: employee_id,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            alert('P/O Printed');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}