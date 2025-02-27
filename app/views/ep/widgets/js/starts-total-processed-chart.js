var StartsTotalProcessedChart;

function LoadStartsTotalProcessedChart() {

    $('#starts-total-processed-loader').removeClass('hidden');

    var type = $('#starts-total-processed-form #type option:selected').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/starts/processed/list?type=' + type + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#starts-total-processed-loader').addClass('hidden');

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
                    borderColor: data.color,
                    stack: 'Stack 0',
                    order: 2,
                    datalabels: {
                        color: ''
                    }
                }
                for (var i = 0; i < data.periods.length; i++) {
                    var month_idx = rpt_months.indexOf(data.periods[i].period);
                    item.data[month_idx] = data.periods[i].total;
                }
                chart_items.push(item);
            });

            var ctx = $('#starts-total-processed-chart');
            if (StartsTotalProcessedChart) {
                StartsTotalProcessedChart.destroy();
            }
            StartsTotalProcessedChart = new Chart(ctx, {
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
                borderColor: 'rgb(0,0,0,0.5)',
                tension: .5,
                data: total_processed,
                order: 1,
                datalabels: {
                    color: 'black',
                    anchor: 'end',
                    align: 'top'
                }
            }
            StartsTotalProcessedChart.data.datasets.push(lineTotal);
            StartsTotalProcessedChart.update();
        },
        error: function (handle, status, error) {
            console.log('LoadStartsTotalProcessedChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadStartsTotalProcessedChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
