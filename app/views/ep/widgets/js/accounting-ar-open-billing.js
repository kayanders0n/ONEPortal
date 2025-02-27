var AccountingAROpenBillingTable;

function GetAccountingAROpenBillingData() {

    $('#accounting-ar-open-billing-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/accounting/ar/open-billing/list?by_type=1&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#accounting-ar-open-billing-loader').addClass('hidden');

            if (AccountingAROpenBillingTable) {
                AccountingAROpenBillingTable.clear().destroy();
            }
            AccountingAROpenBillingTable = $('#accounting-ar-open-billing-data').DataTable({
                ordering: false,
                filter: false,
                paging: false,
                info: false,
                language: {
                    zeroRecords: "No Open Billing Tasks"
                },
                columnDefs: [
                    {
                        targets: 2, // count
                        className: "text-right",
                    },
                ],
                createdRow: function( row, data, dataIndex){
                    if( data[2] > 30){
                        $(row).addClass('danger');
                    }
                }
            });

            $.each(data.results, function (key, item) {

                var add_data = [];

                var type_code = item.type.code;
                var type_name = item.type.name;
                var type_count = item.type.total;

                add_data.push(type_code);
                add_data.push(type_name);
                add_data.push(type_count);

                AccountingAROpenBillingTable.row.add(add_data).draw();
            });
        },
        error: function (handle, status, error) {
            console.log('GetAccountingAROpenBillingData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    GetAccountingAROpenBillingData();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});



