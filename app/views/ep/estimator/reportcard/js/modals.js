function showEstimatorModal(item_id, company_id, modal_type) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/reportcard/show/' + item_id + '/' + company_id + '?tick=' + Math.random(),
        success: function (data) {
            var $item              = data.result.item;
            var item_id            = $item.item_id;
            var project_num        = $item.project_num;
            var project_name       = $item.project_name;
            var project_crossroads = $item.project_crossroads;
            var project_note       = $item.project_note;
            var price_increase     = $item.proposal_price_increase;
            var created_on         = $item.created_on;
            var created_by         = $item.created_by;
            var modified_on        = $item.modified_on;
            var modified_by        = $item.modified_by;

            $('#estimator-' + modal_type + '-body #project-name').html('(' + project_num + ') ' + project_name);
            $('#estimator-' + modal_type + '-body #project-crossroads').html(project_crossroads);
            $('#estimator-' + modal_type + '-body #project-note').html(project_note);

            $('#estimator-' + modal_type + '-body #created-on').html(created_on);
            $('#estimator-' + modal_type + '-body #created-by').html(created_by);
            $('#estimator-' + modal_type + '-body #modified-on').html(modified_on);
            $('#estimator-' + modal_type + '-body #modified-by').html(modified_by);

            $('#form-estimator-' + modal_type + ' #item-id').val(item_id);
            $('#form-estimator-' + modal_type + ' #company-id').val(company_id);
            $('#form-estimator-' + modal_type + ' #price-increase').val(price_increase);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-estimator-' + modal_type).modal();
}

function saveEstimatorReportCardData() {
    var item_id = parseInt($('#form-estimator-edit #item-id').val());
    var company_id = parseInt($('#form-estimator-edit #company-id').val());
    var price_increase = parseInt($('#form-estimator-edit #price-increase').val());

    var user_name = $('#form-estimator-edit #user-name').val();

    $.ajax({
        type: 'post',
        url: '/estimator/reportcard/update/' + item_id,
        data: {
            company_id: company_id,
            price_increase: price_increase,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {

            if (price_increase == 0) {
                $('#item-' + item_id + ' #increase-icon').html('');
            } else if (price_increase == 1) {
                $('#item-' + item_id + ' #increase-icon').html('<i class="fas fa-arrow-alt-circle-up" style="float: right; color: forestgreen; font-size: 1.5em; margin-right: 2px;" title="Price Increase Done!"></i>');
            } else if (price_increase == 2) {
                $('#item-' + item_id + ' #increase-icon').html('<i class="far fa-arrow-alt-circle-up" style="float: right; color: dimgray; font-size: 1.5em; margin-right: 2px;" title="Price Increase Pending..."></i>');
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}