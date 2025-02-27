var EstimatorProfitMarginChart;

function LoadEstimatorProfitMarginChart() {
    var company_id = $('#estimator-profit-margin-form #company option:selected').val();
    var project_site = $('#estimator-profit-margin-form #project-site option:selected').val();

    var label_values = [];
    var profit_margin = [];

    $('#estimator-profit-margin-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/profitmargin/list?company_id=' + company_id + '&project_site=' + project_site + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-profit-margin-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var profitmargin = item.item.profit_margin;

                if (label_values.indexOf(employee_name) == -1) {
                    label_values.push(employee_name);
                    profit_margin.push(0);
                }

                var idx = label_values.indexOf(employee_name);

                profit_margin[idx] = profitmargin;

            });

            var ctx = $('#estimator-profit-margin-chart');
            if (EstimatorProfitMarginChart) {
                EstimatorProfitMarginChart.destroy();
            }
            EstimatorProfitMarginChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Profit Margin %',
                        data: profit_margin,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top',
                            formatter: function(value, context) {
                                return value + '%';
                            }
                        }
                    }
                    ],
                },
                plugins: [ChartDataLabels],
                options: {
                    plugins: {
                        legend: false
                    },
                    interaction: {
                        intersect: false,
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
            console.log('LoadEstimatorProfitMarginChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorProfitMarginChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
