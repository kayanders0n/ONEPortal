var EstimatorBillingAdjChart;

function LoadEstimatorBillingAdjChart() {
    var company_id = $('#estimator-billing-adj-form #company option:selected').val();
    var days_old = $('#estimator-billing-adj-form #days-old option:selected').val();

    var label_values = [];
    var billing_adj_values = [];

    $('#estimator-billing-adj-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/billingadj/list?company_id=' + company_id + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-billing-adj-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var total = item.item.item_count;

                label_values.push(employee_name);
                billing_adj_values.push(total);

            });

            var ctx = $('#estimator-billing-adj-chart');
            if (EstimatorBillingAdjChart) {
                EstimatorBillingAdjChart.destroy();
            }
            EstimatorBillingAdjChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Billing Adjustments',
                        data: billing_adj_values,
                        backgroundColor: 'rgba(0, 128, 0, 0.2)',
                        borderColor: 'rgba(0, 128, 0, 1)',
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
            console.log('LoadEstimatorBillingAdjChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorBillingAdjChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
