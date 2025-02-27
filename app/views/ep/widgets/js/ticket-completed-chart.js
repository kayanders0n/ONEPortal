var TicketCompletedChart;

function LoadTicketCompletedChart() {
    var company_id = $('#ticket-completed-form #company option:selected').val();
    var company_site = $('#ticket-completed-form #site option:selected').val();
    var ticket_type = $('#ticket-completed-form #ticket-type option:selected').val();

    var label_values = ['120 days','90 days','60 days','30 days'];
    var data_values = [];

    $('#ticket-completed-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tickets/completed/list?company_id=' + company_id + '&company_site=' + company_site + '&ticket_type=' + ticket_type + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#ticket-completed-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var total = item.item.total;
                data_values.push(total);

            });

            var ctx = $('#ticket-completed-chart');
            if (TicketCompletedChart) { TicketCompletedChart.destroy(); }
            TicketCompletedChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: ticket_type+' Tickets',
                        data: data_values,
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
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
            console.log('LoadSchedPhaseCompletedChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadTicketCompletedChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();

});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
