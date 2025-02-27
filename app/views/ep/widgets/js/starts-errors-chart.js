var StartsErrorsChart;

function LoadStartsErrorsChart() {

    $('#starts-errors-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/starts/processed/list?type=STARTS&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#starts-errors-loader').addClass('hidden');

            var rpt_months = [];
            var employee_list = [];
            var total_processed = [];
            var total_errors = [];

            collectStartsData(data.results, rpt_months, employee_list, total_processed, total_errors);

            var chart_items = [];

            $.each(employee_list, function (key, data) {
                var item = {
                    type: 'bar',
                    label: data.name,
                    data: new Array(rpt_months.length),
                    backgroundColor: data.color,
                    stack: 'Stack 0',
                    order: 2,
                    datalabels: {
                        color: ''
                    }
                }
                for (var i = 0; i < data.periods.length; i++) {
                    var month_idx = rpt_months.indexOf(data.periods[i].period);
                    item.data[month_idx] = data.periods[i].errors;
                }
                 chart_items.push(item);
            });

            var ctx = $('#starts-errors-chart');
            if (StartsErrorsChart) {
                StartsErrorsChart.destroy();
            }
            StartsErrorsChart = new Chart(ctx, {
                data: {
                    labels: rpt_months,
                    datasets: chart_items
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    interaction: {
                        intersect: false,
                        mode: 'x',
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: '5%'
                        }
                    }
                }
            });

            var lineTotal = {
                type: 'scatter',
                label: 'Total',
                backgroundColor: 'black',
                tension: 0.5,
                data: total_errors,
                order: 1,
                datalabels: {
                    color: 'black',
                    anchor: 'end',
                    align: 'top'
                }
            }

            //var lineGoal = {
            //    type: 'line',
            //    label: 'Goal',
            //    backgroundColor: 'rgb(0,0,0,0.1)',
            //    borderColor: 'rgb(0,0,0,0.1)',
            //    data: [2,2,2,2,2,2,2,2,2,2,2,2],
            //    borderDash: [5, 5],
            //    order: 1,
            //    datalabels: {
            //        color: ''
            //    }
            //}

            StartsErrorsChart.data.datasets.push(lineTotal);
           // StartsErrorsChart.data.datasets.push(lineGoal);
            StartsErrorsChart.update();
        },
        error: function (handle, status, error) {
            console.log('LoadStartsErrorsChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadStartsErrorsChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
