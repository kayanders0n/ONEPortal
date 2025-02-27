function showHyphenModal(item_id, modal_type) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/hyphen/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {

            var $item = data.result.hyphen;
            var item_id = $item.item_id;
            var account_code = $item.account_code;
            var project_code = $item.project_code;
            var jobsite_address = $item.jobsite_address;
            var jobsite_lotnum = $item.jobsite_lotnum;
            var action_name = $item.action_name;
            var created_on = $item.created_on;
            var processed_on = $item.processed_on;
            var deleted_on = $item.deleted_on;
            var modified_on = $item.modified_on;
            var modified_by = $item.modified_by;

            $('#hyphen-' + modal_type + '-body #action-name').html(action_name);
            $('#hyphen-' + modal_type + '-body #jobsite-address').html(jobsite_address);
            $('#hyphen-' + modal_type + '-body #created-on').html(created_on);
            $('#hyphen-' + modal_type + '-body #modified-on').html(modified_on);
            $('#hyphen-' + modal_type + '-body #modified-by').html(modified_by);
            $('#hyphen-' + modal_type + '-body #processed-on').html(processed_on);
            $('#hyphen-' + modal_type + '-body #deleted-on').html(deleted_on);

            $('#form-hyphen-' + modal_type + ' #item-id').val(item_id);
            $('#form-hyphen-' + modal_type + ' #account-code').val(account_code);
            $('#form-hyphen-' + modal_type + ' #project-code').val(project_code);
            $('#form-hyphen-' + modal_type + ' #jobsite-lotnum').val(jobsite_lotnum);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-hyphen-' + modal_type).modal();
}

function saveHyphenData() {
    var item_id = parseInt($('#form-hyphen-edit #item-id').val());
    var account_code = $('#form-hyphen-edit #account-code').val();
    var project_code = $('#form-hyphen-edit #project-code').val();
    var jobsite_lotnum = $('#form-hyphen-edit #jobsite-lotnum').val();
    var user_name = $('#form-hyphen-edit #user-name').val();

    $.ajax({
        type: 'post',
        url: '/hyphen/update/' + item_id,
        data: {
            account_code: account_code,
            project_code: project_code,
            jobsite_lotnum: jobsite_lotnum,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {

            var update_table = $('#hyphen-data').DataTable();
            var update_row = $('#item-' + item_id);
            var update_data = update_table.row(update_row).data();

            update_data[0] = project_code;
            update_data[4] = jobsite_lotnum;

            update_table.row(update_row).data(update_data).draw();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function deleteHyphenData() {
    var item_id = parseInt($('#form-hyphen-edit #item-id').val());
    var user_name = $('#form-hyphen-edit #user-name').val();

    if (confirm('Are you sure?')) {
        $.ajax({
            type: 'post',
            url: '/hyphen/update/' + item_id,
            data: {
                delete_item: 'YES',
                user_name: user_name,
                tick: Math.random()
            },
            dataType: 'json',
            success: function (data) {
                $('#item-' + item_id).slideUp('slow', function () {
                    $(this).remove()
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + ': ' + errorThrown);
            }
        });
    }
}

