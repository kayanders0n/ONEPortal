var EstimatorARQueueChart;

function LoadEstimatorARQueueChart() {
    var company_id = $('#estimator-ar-queue-form #company option:selected').val();

    var label_values = [];
    var ar_queue_values = [];

    $('#estimator-ar-queue-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/accounting/ar/process-queue/list?company_id=' + company_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-ar-queue-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var total = item.item.item_count;

                label_values.push(employee_name);
                ar_queue_values.push(total);

            });

            var ctx = $('#estimator-ar-queue-chart');
            if (EstimatorARQueueChart) {
                EstimatorARQueueChart.destroy();
            }
            EstimatorARQueueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'A/R Queue',
                        data: ar_queue_values,
                        backgroundColor: 'rgba(138,43,226, 0.4)',
                        borderColor: 'rgba(138,43,226, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top'
                        }
                    }],

                },
                plugins: [ChartDataLabels],
                options: {
                    plugins: {
                        legend: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: '5%'
                        }
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadEstimatorARQueueChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorARQueueChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
