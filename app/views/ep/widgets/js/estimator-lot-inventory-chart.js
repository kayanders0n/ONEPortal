var EstimatorLotInventoryChart;

function LoadEstimatorLotInventoryChart() {
    var company_id = $('#estimator-lot-inventory-form #company option:selected').val();
    var project_site = $('#estimator-lot-inventory-form #project-site option:selected').val();

    var label_values = [];
    var lots_remaining_values = [];
    var current_jobs_values = [];

    $('#estimator-lot-inventory-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/estimator/lotinventory/list?company_id=' + company_id + '&project_site=' + project_site + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#estimator-lot-inventory-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var employee_name = item.item.employee_name;
                var lotsremaining = item.item.lots_remaining_count;
                var currentjobs = item.item.current_jobs_count;

                if (label_values.indexOf(employee_name) == -1) {
                    label_values.push(employee_name);
                    lots_remaining_values.push(0);
                    current_jobs_values.push(0);
                }

                var idx = label_values.indexOf(employee_name);

                lots_remaining_values[idx] = lotsremaining;
                current_jobs_values[idx] = currentjobs;

            });

            var ctx = $('#estimator-lot-inventory-chart');
            if (EstimatorLotInventoryChart) {
                EstimatorLotInventoryChart.destroy();
            }
            EstimatorLotInventoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Current Jobs',
                        data: current_jobs_values,
                        backgroundColor: 'rgba(138,43,226, 0.4)',
                        borderColor: 'rgba(138,43,226, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'start',
                            align: 'bottom'
                        },
                        stack: 'Stack 0',
                    }, {
                        label: 'Lots Remaining',
                        data: lots_remaining_values,
                        backgroundColor: 'rgba(138,43,226, 0.1)',
                        borderColor: 'rgba(138,43,226, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top'
                        },
                        stack: 'Stack 0',
                    }
                    ],

                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    interaction: {
                        intersect: false,
                        mode: 'x',
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grace: '5%'
                        }
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadEstimatorLotInventoryChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadEstimatorLotInventoryChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
