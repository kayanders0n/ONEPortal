var EstimatorStartsProcessedLateChart;

function LoadEstimatorStartsProcessedLateChart() {
    var company_id = $('#estimator-starts-processed-late-form #company option:selected').val();
    var days_old = $('#estimator-starts-processed-late-form #days-old option:selected').val();

    var label_values = [];
    var total_values = [];

    $('#estimator-starts-processed-late-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/startsprocessedlate/list?company_id=' + company_id + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-starts-processed-late-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var total = item.item.item_count;

                label_values.push(employee_name);
                total_values.push(total);

            });

            var ctx = $('#estimator-starts-processed-late-chart');
            if (EstimatorStartsProcessedLateChart) {
                EstimatorStartsProcessedLateChart.destroy();
            }
            EstimatorStartsProcessedLateChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Starts Processed Late',
                        data: total_values,
                        backgroundColor: 'rgba(255, 140, 0, 0.2)',
                        borderColor: 'rgba(255, 140, 0, 1)',
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
            console.log('LoadEstimatorStartsProcessedLateChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorStartsProcessedLateChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
