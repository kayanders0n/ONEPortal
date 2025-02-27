var TicketPOWaitingChart;

function LoadTicketPOWaitingChart() {
    // hide the by builder top 10
    $('#ticket-po-waiting-builder-data').addClass('hidden');

    var company_id = $('#ticket-po-waiting-form #company option:selected').val();
    var company_site = $('#ticket-po-waiting-form #site option:selected').val();
    var days_old = $('#ticket-po-waiting-form #days-old option:selected').val();

    var label_values = ['Awaiting', 'Approved'];
    var data_values = [];

    $('#ticket-po-waiting-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tickets/po-waiting/list?company_id=' + company_id + '&company_site=' + company_site + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#ticket-po-waiting-loader').addClass('hidden');

            var total_count = 0;
            var quoted_count = 0;
            $.each(data.results, function (key, item) {
                var data_type = item.item.data_type;
                var item_count = item.item.total_count;

                if (data_type == 'QUOTED') {
                    quoted_count += item_count;
                } else if (data_type == 'TOTAL') {
                    total_count += item_count;
                }
            });

            data_values.push(quoted_count);
            data_values.push(total_count-quoted_count);


            var ctx = $('#ticket-po-waiting-chart');
            if (TicketPOWaitingChart) { TicketPOWaitingChart.destroy(); }
            TicketPOWaitingChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: label_values,
                    datasets: [{
                        data: data_values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1,
                        datalabels: {
                            color: 'black'
                        }
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    maintainAspectRatio: false,
                    aspectRatio: 2
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadSchedPhaseCompletedChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadTicketPOWaitingChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();

});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
