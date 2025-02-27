function loadBirthdaysData() {

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/employee/list/list?employee_type=birthday&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#birthdays-data').html('');

            $.each(data.results, function (key, item) {
                $item = item.employee;
                $('#birthdays-data').append('<li class="list-group-item">' + $item.first_name + ' ' + $item.last_name + ' <span class="label label-warning label-as-badge" style="float:right; font-size:1.1em;" >' + $item.birthdate_short + '</span></li>');


            });
        },
        error: function (handle, status, error) {
            console.log('loadBirthdaysData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    loadBirthdaysData();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
