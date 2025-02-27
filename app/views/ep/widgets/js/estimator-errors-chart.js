var EstimatorErrorsChart;

function LoadEstimatorErrorsChart() {
    var company_id = $('#estimator-errors-form #company option:selected').val();
    var days_old = $('#estimator-errors-form #days-old option:selected').val();

    var label_values = [];
    var error_values = [];

    $('#estimator-errors-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/errors/list?company_id=' + company_id + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-errors-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var total = item.item.item_count;

                label_values.push(employee_name);
                error_values.push(total);

            });

            var ctx = $('#estimator-errors-chart');
            if (EstimatorErrorsChart) {
                EstimatorErrorsChart.destroy();
            }
            EstimatorErrorsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Estimator Errors',
                        data: error_values,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
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
            console.log('LoadEstimatorErrorsChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorErrorsChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
