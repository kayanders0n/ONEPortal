function showTWWVRModal(item_id, modal_type) {
    if (modal_type == 'process') {
        // defaults
        $('#work-percent-done').val('100');
        $('#video-comment').val('');
    }

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/twwvr/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {

            var $item = data.result;
            var item_id = $item.twwvr.item_id;
            var record_type = $item.twwvr.record_type;
            var activity_date = $item.twwvr.activity_date;
            var employee_name = $item.employee.name;
            var task_num = $item.task.user_num;
            var task_name = $item.task.name;
            var job_num = $item.job.user_num;
            var job_name = '<strong>' + $item.project.num + '</strong>- ' + $item.project.name + ' Lot: <strong>' + $item.job.lot + '</strong>';
            var recorded_on = $item.file.created_on;
            var network = $item.file.network;
            var description = '<span style="color: red; font-weight: bold;">Not Linked</span>';
            if ($item.task.id) {
                description = task_name;
            } else if ($item.job.id) {
                description = job_name;
            }

            var created_on = $item.twwvr.created_on;
            var processed_on = $item.twwvr.processed_on;
            var modified_on = $item.twwvr.modified_on;
            var modified_by = $item.twwvr.modified_by;

            $('#twwvr-' + modal_type + '-body #operation-num').html(job_num + '/' + task_num);
            $('#twwvr-' + modal_type + '-body #activity-date').html(activity_date);
            $('#twwvr-' + modal_type + '-body #record-type').html(record_type);
            $('#twwvr-' + modal_type + '-body #employee').html(employee_name);
            $('#twwvr-' + modal_type + '-body #description').html(description);
            $('#twwvr-' + modal_type + '-body #recorded-on').html(recorded_on);
            $('#twwvr-' + modal_type + '-body #network').html(network);
            $('#twwvr-' + modal_type + '-body #created-on').html(created_on);
            $('#twwvr-' + modal_type + '-body #modified-on').html(modified_on);
            $('#twwvr-' + modal_type + '-body #modified-by').html(modified_by);
            $('#twwvr-' + modal_type + '-body #processed-on').html(processed_on);

            $('#form-twwvr-' + modal_type + ' #item-id').val(item_id);
            $('#form-twwvr-' + modal_type + ' #employee-name').val(employee_name);
            $('#form-twwvr-' + modal_type + ' #task-num').val(task_num);
            $('#form-twwvr-' + modal_type + ' #job-num').val(job_num);


            if (modal_type == 'process') {
                var s3_url = $item.file.s3_url;
                var file_name = $item.file.name;
                var file_ext = file_name.substr( (file_name.lastIndexOf('.') +1) );
                file_ext = file_ext.toUpperCase();
                if ((file_ext == 'JPG') || (file_ext == 'PNG')) {
                    showPicture(s3_url);
                } else {
                    playVideo(s3_url);
                }

                $("#process-email-link").unbind("click" ); // first remove the prior binding if any
                $("#process-email-link").click(function(){ emailLink($item.employee.email, record_type, job_num, $item.file.file_url); });

                $("#process-create-task").unbind("click" ); // first remove the prior binding if any
                $("#process-create-task").click(function(){ createTask(); });

                var is_processed = $item.twwvr.processed;
                var is_deleted = $item.twwvr.deleted;

                $('#process-submit').prop('disabled', !((is_processed == 0) && (is_deleted == 0)));
                $('#process-delete').prop('disabled', !((is_processed == 0) && (is_deleted == 0)));
                $('#work-percent-done').prop('disabled', !((is_processed == 0) && (is_deleted == 0) && (record_type == 'TICKET')));


            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-twwvr-' + modal_type).modal();
}

function saveTWWVRData() {
    var item_id = parseInt($('#form-twwvr-edit #item-id').val());
    var record_type = $('#form-twwvr-edit #record-type').val();
    var task_num = $('#form-twwvr-edit #task-num').val();
    var job_num = $('#form-twwvr-edit #job-num').val();
    var user_name = $('#form-twwvr-edit #user-name').val();

    $.ajax({
        type: 'post',
        url: '/twwvr/update/' + item_id,
        data: {
            record_type: record_type,
            task_num: task_num,
            job_num: job_num,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {

            var update_table = $('#twwvr-data').DataTable();
            var update_row = $('#item-' + item_id);
            var update_data = update_table.row(update_row).data();

            update_data[3] = task_num;
            update_data[4] = job_num;

            update_table.row(update_row).data(update_data).draw();

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}


function processTWWVRData() {
    var item_id = parseInt($('#form-twwvr-process #item-id').val());
    var user_name = $('#form-twwvr-process #user-name').val();
    var work_percent_done = $('#form-twwvr-tasks #work-percent-done').val();
    var video_comment = $('#video-comment').val();

    if (confirm('Are you sure?')) {
        $.ajax({
            type: 'post',
            url: '/twwvr/update/' + item_id,
            data: {
                process_item: 'YES',
                work_percent_done: work_percent_done,
                note: video_comment,
                user_name: user_name,
                tick: Math.random()
            },
            dataType: 'json',
            success: function (data) {
                $('#item-' + item_id).slideUp('slow', function () {
                    $(this).remove()
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + ': ' + errorThrown);
            }
        });
    }
}

function deleteTWWVRData(modal_type) {
    var item_id = parseInt($('#form-twwvr-' + modal_type + ' #item-id').val());
    var user_name = $('#form-twwvr-' + modal_type + ' #user-name').val();

    if (confirm('Are you sure?')) {
        $.ajax({
            type: 'post',
            url: '/twwvr/update/' + item_id,
            data: {
                delete_item: 'YES',
                user_name: user_name,
                tick: Math.random()
            },
            dataType: 'json',
            success: function (data) {
                $('#item-' + item_id).slideUp('slow', function () {
                    $(this).remove()
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus + ': ' + errorThrown);
            }
        });
    }
}


function playVideo(URL) {
    // hide the picture viewer and show the video
    $('#twwvr-picture-box').hide();
    $('#twwvr-video-box').show();

    vPlayer = videojs('twwvr-video');
    vPlayer.src(URL);
    vPlayer.play();

    return false;
}

function stopVideo() {
    vPlayer = videojs('twwvr-video');
    vPlayer.pause();
}

function showPicture(URL) {
    stopVideo();

    // hide the video player and show the picture;
    $('#twwvr-video-box').hide();
    $('#twwvr-picture-box').show();

    $('#twwvr-picture img').attr('src', '/assets/images/main/loading_640x320.png');
    $('#twwvr-picture img').attr('src', URL);
    $('#twwvr-picture img').attr('data-original', URL);

    return false;
}

function emailLink(email, type, job_num, URL) {
    window.location.href  = 'mailto:' + email + '?subject=TWWVR Link ' + type + ' Job ' + job_num + '  ' + URL;
}

function createTask() {
    alert("I'm guessing you wish this feature actually worked.  Sorry it doesn't yet.  This will be available in a future version...I hope.");
}




