var TWWVRProductivityTable;

function GetTWWVRProductivityData() {
    var company_id = $('#twwvr-productivity-form #company option:selected').val();
    var company_site = $('#twwvr-productivity-form #site option:selected').val();
    var days_old = $('#twwvr-productivity-form #days-old option:selected').val();

    $('#twwvr-productivity-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/productivity/list?company_id=' + company_id + '&company_site=' + company_site + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#twwvr-productivity-loader').addClass('hidden');

            if (TWWVRProductivityTable) {
                TWWVRProductivityTable.clear().destroy();
            }
            TWWVRProductivityTable = $('#twwvr-productivity-data').DataTable({
                ordering: false,
                filter: false,
                paging: false,
                info: false,
                language: {
                    zeroRecords: "No Visual Record Data"
                },
                columnDefs: [
                    {
                        targets: 2, // site
                        className: "text-center",
                    },
                    {
                        targets: 3, // megabytes
                        className: "text-right",
                    },
                    {
                        targets: 4, // files uploaded
                        className: "text-right",
                    },
                ],
            });

            $.each(data.results, function (key, item) {

                var add_data = [];

                var employee_num = item.employee.num;
                var employee_name = item.employee.name;
                var employee_site = item.employee.site;
                var upload_megabytes = item.upload.megabytes;
                var upload_total = item.upload.total;

                add_data.push(employee_num);
                add_data.push(employee_name);
                add_data.push(employee_site);
                add_data.push(upload_megabytes);
                add_data.push(upload_total);

                TWWVRProductivityTable.row.add(add_data).draw();
            });
        },
        error: function (handle, status, error) {
            console.log('GetTWWVRProductivityData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    GetTWWVRProductivityData();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});



