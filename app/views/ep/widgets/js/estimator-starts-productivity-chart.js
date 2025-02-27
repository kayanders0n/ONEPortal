var EstimatorStartsProductivityChart;

function LoadEstimatorStartsProductivityChart() {
    var company_id = $('#estimator-starts-productivity-form #company option:selected').val();
    var days_old = $('#estimator-starts-productivity-form #days-old option:selected').val();

    var label_values = [];
    var changes_data_values = [];
    var phases_data_values = [];

    $('#estimator-starts-productivity-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/startsproductivity/list?company_id=' + company_id + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-starts-productivity-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var review_type = item.item.review_type;
                var employee_name = item.item.employee_name;
                var total = item.item.item_count;

                if (label_values.indexOf(employee_name) == -1) {
                    label_values.push(employee_name);
                    phases_data_values.push(0);
                    changes_data_values.push(0);
                }

                var idx = label_values.indexOf(employee_name);

                if (review_type == 'PHASE') {
                    phases_data_values[idx] = total;
                } else if (review_type == 'CHANGE') {
                    changes_data_values[idx] = total;
                }
            });

            var ctx = $('#estimator-starts-productivity-chart');
            if (EstimatorStartsProductivityChart) {
                EstimatorStartsProductivityChart.destroy();
            }
            EstimatorStartsProductivityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Phases',
                        data: phases_data_values,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top'
                        }
                    }, {
                        label: 'Changes',
                        data: changes_data_values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top'
                        }
                    }
                    ],

                },
                plugins: [ChartDataLabels],
                options: {
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
            console.log('LoadEstimatorStartsProductivityChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorStartsProductivityChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
