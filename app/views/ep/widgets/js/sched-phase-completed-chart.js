var SchedPhaseCompletedChart;

function LoadSchedPhaseCompletedChart() {
    var company_id = $('#sched-phase-completed-form #company option:selected').val();
    var company_site = $('#sched-phase-completed-form #site option:selected').val();
    var days_old = $('#sched-phase-completed-form #days-old option:selected').val();

    var label_values = [];
    var completed_data_values = [];
    var scheduled_data_values = [];

    $('#sched-phase-completed-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/schedule/phase-completed/list?company_id=' + company_id + '&company_site=' + company_site + '&days_old=' + days_old + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#sched-phase-completed-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var data_type = item.item.data_type;
                var phase_name = item.item.phase_name;
                var total = item.item.total;

                if (data_type == 'COMPLETED') {
                    label_values.push(phase_name);
                    completed_data_values.push(total);
                } else if (data_type == 'SCHEDULED') {
                    scheduled_data_values.push(total);
                }
            });

            var ctx = $('#sched-phase-completed-chart');
            if (SchedPhaseCompletedChart) {
                SchedPhaseCompletedChart.destroy();
            }
            SchedPhaseCompletedChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Completed',
                        data: completed_data_values,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        datalabels: {
                            color: 'black',
                            anchor: 'end',
                            align: 'top'
                        }
                    }, {
                        label: 'Scheduled',
                        data: scheduled_data_values,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
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
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('LoadSchedPhaseCompletedChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadSchedPhaseCompletedChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
