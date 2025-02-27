var TicketCompletedChart2;

function LoadTicketCompletedChart2() {
    var company_id = $('#ticket-completed-form2 #company option:selected').val();
    var company_site = $('#ticket-completed-form2 #site option:selected').val();
    var ticket_type = $('#ticket-completed-form2 #ticket-type option:selected').val();

    var label_values = ['120 days','90 days','60 days','30 days'];
    var data_values = [];

    $('#ticket-completed-loader2').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tickets/completed/list?company_id=' + company_id + '&company_site=' + company_site + '&ticket_type=' + ticket_type + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#ticket-completed-loader2').addClass('hidden');

            $.each(data.results, function (key, item) {

                var total = item.item.total;
                data_values.push(total);

            });

            var ctx2 = $('#ticket-completed-chart2');
            if (TicketCompletedChart2) { TicketCompletedChart2.destroy(); }
            TicketCompletedChart2 = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: ticket_type+' Tickets',
                        data: data_values,
                        backgroundColor: 'rgba(255, 100, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }],

                },
                options: {
                    fill: true,
                    tension: 0.3,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadSchedPhaseCompletedChart2: ' + error + ' ' + status);
        }
    })
}


$(document).ready(function () {
    LoadTicketCompletedChart2();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();

});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
