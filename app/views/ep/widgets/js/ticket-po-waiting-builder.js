var TicketPOWaitingBuilderTable;

function GetTicketPOWaitingBuilderData() {
    var company_id = $('#ticket-po-waiting-form #company option:selected').val();
    var company_site = $('#ticket-po-waiting-form #site option:selected').val();
    var days_old = $('#ticket-po-waiting-form #days-old option:selected').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tickets/po-waiting/list?company_id=' + company_id + '&company_site=' + company_site + '&days_old=' + days_old + '&by_builder=1&limit=10&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#ticket-po-waiting-builder-data').removeClass('hidden');
            if (TicketPOWaitingBuilderTable) {
                TicketPOWaitingBuilderTable.clear().destroy();
            }
            TicketPOWaitingBuilderTable = $('#ticket-po-waiting-builder-data').DataTable({
                ordering: false,
                filter: false,
                paging: false,
                info: false,
                language: {
                    zeroRecords: "No PO Approval Waiting for Builders"
                },
                columnDefs: [
                    {
                        targets: 2, // total amount
                        className: "text-right",
                    },
                    {
                        targets: 3, // PO count
                        className: "text-right",
                    },
                ],
            });

            $.each(data.results, function (key, item) {

                var add_data = [];

                var builder_num = item.builder.num;
                var builder_name = item.builder.name;
                var total_amount = item.item.total_amount;
                var total_count = item.item.total_count;

                add_data.push(builder_num);
                add_data.push(builder_name);
                add_data.push(total_amount);
                add_data.push(total_count);

                TicketPOWaitingBuilderTable.row.add(add_data).draw();
            });
        },
        error: function (handle, status, error) {
            console.log('GetTicketPOWaitingBuilderData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    //GetTicketPOWaitingBuilderData();
    // wait for them to click on it
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});



