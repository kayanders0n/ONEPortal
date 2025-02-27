function showBidsModal(item_id, modal_type) {
    $('#bid-date-due').datepicker();
    $('#bid-date-sent').datepicker();
    $('#bid-date-award').datepicker();

    if (item_id == 'new') { // new bid record
        $('#form-bids-' + modal_type)[0].reset();
        $('#form-bids-' + modal_type + ' #item-id').val(item_id);
        $('#form-bids-' + modal_type + ' #company-id').val(''); // no company by default

        $('#modal-bids-' + modal_type + ' #bid-data-misc').hide();
        $('#modal-bids-' + modal_type + ' #modal-bids-edit-label').html('New Project Bid');


        $('#modal-bids-' + modal_type).modal();
        return false;
    }

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/bids/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {
            var $item          = data.result.item;
            var item_id        = $item.item_id;
            var bid_num        = $item.bid_num;
            var customer_name  = $item.customer_name;
            var project_name   = $item.project_name;
            var project_series = htmlDecode($item.project_series);
            var project_city   = htmlDecode($item.project_city);
            var project_area   = $item.project_area;
            var lot_count      = $item.lot_count;
            var bid_date_due   = $item.bid_date_due;
            var bid_date_sent  = $item.bid_date_sent;
            var bid_date_award = $item.bid_date_award;
            var bid_note       = $item.bid_note;

            var created_on         = $item.created_on;
            var created_by         = $item.created_by;
            var modified_on        = $item.modified_on;
            var modified_by        = $item.modified_by;

            $('#modal-bids-' + modal_type + ' #modal-bids-edit-label').html('Edit Project Bid# ' + bid_num);
            $('#modal-bids-' + modal_type + ' #bid-data-misc').show(); // may have been hidden for new bid

            $('#bids-' + modal_type + '-body #bid-note').html(bid_note);
            $('#bids-' + modal_type + '-body #created-on').html(created_on);
            $('#bids-' + modal_type + '-body #created-by').html(created_by);
            $('#bids-' + modal_type + '-body #modified-on').html(modified_on);
            $('#bids-' + modal_type + '-body #modified-by').html(modified_by);

            $('#form-bids-' + modal_type + ' #item-id').val(item_id);
            $('#form-bids-' + modal_type + ' #customer-name').val(customer_name);
            $('#form-bids-' + modal_type + ' #project-name').val(project_name);
            $('#form-bids-' + modal_type + ' #project-series').val(project_series);
            $('#form-bids-' + modal_type + ' #project-city').val(project_city);
            $('#form-bids-' + modal_type + ' #project-area').val(project_area);
            $('#form-bids-' + modal_type + ' #lot-count').val(lot_count);
            $('#form-bids-' + modal_type + ' #bid-date-due').val(bid_date_due);
            $('#form-bids-' + modal_type + ' #bid-date-sent').val(bid_date_sent);
            $('#form-bids-' + modal_type + ' #bid-date-award').val(bid_date_award);

            bidCompanyChange($item);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-bids-' + modal_type).modal();
}


function bidCompanyChange(data) {

    var item_id =  parseInt($('#form-bids-edit #item-id').val());
    var company_id =  parseInt($('#form-bids-edit #company-id option:selected').val());

    if (company_id != 0) {
        if (data == undefined) {
            showBidsModal(item_id, 'bids'); // bad modal name so it won't wipe out the existing data that may have changed on the dialog
            return false;
        }

        var profit_margin = 0;
        var bid_status = '';

        switch (company_id) {
            case 5633: // plumbing
                profit_margin = data.plumbing.margin;
                bid_status    = data.plumbing.status;
                break;
            case 21440: // concrete
                profit_margin = data.concrete.margin;
                bid_status    = data.concrete.status;
                break;
            case 21442: // framing
                profit_margin = data.framing.margin;
                bid_status    = data.framing.status;
                break;
            case 21444: // door and trim
                profit_margin = data.door_trim.margin;
                bid_status    = data.door_trim.status;
                break;
        }

        $('#form-bids-edit #bid-profit-margin').val(profit_margin);
        $('#form-bids-edit #bid-profit-margin').prop( "disabled", false);
        $('#form-bids-edit #bid-status').val(bid_status);
        $('#form-bids-edit #bid-status').prop( "disabled", false);
    } else {
        $('#form-bids-edit #bid-profit-margin').val('');
        $('#form-bids-edit #bid-profit-margin').prop( "disabled", true );
        $('#form-bids-edit #bid-status').val('');
        $('#form-bids-edit #bid-status').prop( "disabled", true );
    }

}

function saveBidsData() {

    var item_id = $('#form-bids-edit #item-id').val();
    var post_url = '/estimator/bids/update/' + parseInt(item_id);

    if (item_id == 'new') { post_url = '/estimator/bids/add'; }

    var customer_name = $('#form-bids-edit #customer-name').val();
    var project_name = $('#form-bids-edit #project-name').val();
    var project_series = $('#form-bids-edit #project-series').val();
    var project_city = $('#form-bids-edit #project-city').val();
    var project_area = $('#form-bids-edit #project-area').val();
    var lot_count = $('#form-bids-edit #lot-count').val();
    var bid_date_due = $('#form-bids-edit #bid-date-due').val();
    var bid_date_sent = $('#form-bids-edit #bid-date-sent').val();
    var bid_date_award = $('#form-bids-edit #bid-date-award').val();

    var company_id =  parseInt($('#form-bids-edit #company-id option:selected').val());
    var profit_margin = parseFloat($('#form-bids-edit #bid-profit-margin').val());
    var bid_status = $('#form-bids-edit #bid-status').val();

    var user_name = $('#form-bids-edit #user-name').val();

    if ((customer_name == '') || (project_name == '')) {
        alert('Missing required information!'); return false;
    }

    $.ajax({
        type: 'post',
        url: post_url,
        data: {
            customer_name: customer_name,
            project_name: project_name,
            project_series: project_series,
            project_city: project_city,
            project_area: project_area,
            lot_count: lot_count,
            bid_date_due: bid_date_due,
            bid_date_sent: bid_date_sent,
            bid_date_award: bid_date_award,
            company_id: company_id,
            profit_margin: profit_margin,
            bid_status: bid_status,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            loadBidsData();
            $('#modal-bids-edit').modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function htmlDecode(value){
    return $('<div/>').html(value).text();
}