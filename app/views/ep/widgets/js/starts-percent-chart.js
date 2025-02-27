var StartsPercentChart;

function LoadStartsPercentChart() {

    $('#starts-percent-loader').removeClass('hidden');

    var type = $('#starts-percent-form #type option:selected').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/starts/processed/list?type=' + type + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#starts-percent-loader').addClass('hidden');

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
                    data: new Float32Array(rpt_months.length),
                    backgroundColor: data.color,
                    stack: 'Stack 0',
                    order: 2,
                    datalabels: {
                        color: 'black',
                        anchor: 'middle',
                        align: 'middle',
                        formatter: function(value, context) {
                            if (value < 10) return '';
                            return value + '%';
                        }
                    }
                }
                for (var i = 0; i < data.periods.length; i++) {
                    var month_idx = rpt_months.indexOf(data.periods[i].period);
                    item.data[month_idx] = Math.round((data.periods[i].total/total_processed[month_idx])*100);
                }
                 chart_items.push(item);
            });

            var ctx = $('#starts-percent-chart');
            if (StartsPercentChart) {
                StartsPercentChart.destroy();
            }

            StartsPercentChart = new Chart(ctx, {
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
                            grace: '5%',
                            max: 100
                        }
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadStartsPercentChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadStartsPercentChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
