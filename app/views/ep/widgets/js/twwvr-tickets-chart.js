var TWWVRTicketChart;

function LoadTWWVRTicketChart() {
    var company_id = $('#twwvr-ticket-form #company option:selected').val();
    var company_site = $('#twwvr-ticket-form #site option:selected').val();
    var days_old = $('#twwvr-ticket-form #days-old option:selected').val();

    var label_values = ['No Visual Record', 'Visual Record'];
    var data_values = [];

    $('#twwvr-ticket-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/productivity/list?tickets=yes&company_id=' + company_id + '&company_site=' + company_site + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#twwvr-ticket-loader').addClass('hidden');

            var total_count = 0;
            var twwvr_count = 0;
            $.each(data.results, function (key, item) {
                var data_type = item.tickets.data_type;
                var item_count = item.tickets.total_count;

                if (data_type == 'TWWVR') {
                    twwvr_count += item_count;
                } else if (data_type == 'TICKETS') {
                    total_count += item_count;
                }
            });

            data_values.push(total_count-twwvr_count);
            data_values.push(twwvr_count);


            var ctx = $('#twwvr-ticket-chart');
            if (TWWVRTicketChart) { TWWVRTicketChart.destroy(); }
            TWWVRTicketChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: label_values,
                    datasets: [{
                        data: data_values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)'

                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',

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
            console.log('LoadTWWVRTicketChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadTWWVRTicketChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();

});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
