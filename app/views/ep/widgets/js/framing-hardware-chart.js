var FramingHardwareChart;

function LoadFramingHardwareChart() {

    var label_values = [];
    var current_jobs_values = [];

    $('#framing-hardware-loader').removeClass('hidden');

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/framing/communityinfo/list?tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#framing-hardware-loader').addClass('hidden');

            $.each(data.results, function (key, item) {

                var vendor_name = item.hardware_company.name;
                var job_count = item.project.job_count;

                if (label_values.indexOf(vendor_name) == -1) {
                    label_values.push(vendor_name);
                    current_jobs_values.push(0);
                }

                var idx = label_values.indexOf(vendor_name);

                current_jobs_values[idx] += job_count;

            });

            var ctx = $('#framing-hardware-chart');
            if (FramingHardwareChart) {
                FramingHardwareChart.destroy();
            }
            FramingHardwareChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label_values,
                    datasets: [{
                        label: 'Lots',
                        data: current_jobs_values,
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
            console.log('LoadFramingHardwareChart: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadFramingHardwareChart();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
