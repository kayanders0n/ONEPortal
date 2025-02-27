function loadJobNotes() {
    var job_id = $('#job-id').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/show/' + job_id + '?tick=' + Math.random(),
        success: function (data, status, handle) {

            var $item = data.result;

            $('#builder-name').html($item.builder.name);
            $('#project-name').html($item.project.name);
            $('#lot-num').html($item.jobsite.code);
            $('#job-note').html($item.job.note);

            if ($item.company.id == 21440) {
                $('#incident-report-panel').show();
            } else {
                $('#incident-report-panel').hide();
            }

        },
        error: function (handle, status, error) {
            console.log('loadJobNotesData: ' + error + ' ' + status);
        }
    });
}

function updateJobNote() {
    var job_id = parseInt($('#add-note-form #job-id').val());
    var job_note = $('#add-note-form #add-note').val();
    var user_name = $('#add-note-form #user-name').val();

    $.ajax({
        type: 'post',
        url: '/jobs/update/' + job_id,
        data: {
            note: job_note,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            var today = new Date();
            $('#job-note').prepend(user_name + ' ' + today.toLocaleString('en-US') + '<br/>' + job_note + '<br/><br/>');
            $('#add-note-form #add-note').val('');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}

function updateIncidentReport() {
    var job_id = parseInt($('#incident-report-form #job-id').val());
    var user_name = $('#incident-report-form #user-name').val();
    var weather = $('#incident-report-form #weather-note').val();
    var material = $('#incident-report-form #material-note').val();
    var pump = $('#incident-report-form #pump-note').val();
    var supplier = $('#incident-report-form #supplier-note').val();
    var incident_report = '';


    if (weather) {
        incident_report += 'Weather: ' + weather + "\r\n\r\n";
    }
    if (material) {
        incident_report += 'Material/Service: ' + material + "\r\n\r\n";
    }
    if (pump) {
        incident_report += 'Pump: ' + pump + "\r\n\r\n";
    }
    if (supplier) {
        incident_report += 'Supplier Comment: ' + supplier + "\r\n\r\n";
    }

    if (incident_report) {
        incident_report = 'Incident Report:' + "\r\n\r\n" + incident_report;
    }

    if (!incident_report) { return false; }

    $.ajax({
        type: 'post',
        url: '/jobs/update/' + job_id,
        data: {
            note: incident_report,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            var today = new Date();
            $('#job-note').prepend(user_name + ' ' + today.toLocaleString('en-US') + '<br/>' + incident_report.replace(/(\r\n|\r|\n)/g, '<br/>'));
            $('#incident-report-form #weather-note').val('');
            $('#incident-report-form #material-note').val('');
            $('#incident-report-form #pump-note').val('');
            $('#incident-report-form #supplier-note').val('');
        },

        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}