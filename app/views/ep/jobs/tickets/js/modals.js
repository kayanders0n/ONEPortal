function showJobTicketsModal(item_id, modal_type) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/tasks/show/' + item_id + '?tick=' + Math.random(),
        success: function (data) {
            var $item          = data.result;
            var item_id        = $item.task.item_id;
            var ticket_num     = $item.task.num;
            var schedule_start = $item.task.schedule_start;
            var submit_by      = $item.submitted.name;
            var assign_to      = $item.assigned.name;
            var comment        = $item.task.comment;
            var actual_finish  = $item.task.actual_finish;
            var ticket_type    = $item.task.type_code;
            var ticket_name    = $item.task.name;
            var ticket_note    = $item.task.note;


            $('#job-ticket-' + modal_type + '-body #table-id').html(ticket_num);
            $('#job-ticket-' + modal_type + '-body #start-date').html(schedule_start);
            $('#job-ticket-' + modal_type + '-body #submit-by').html(submit_by);
            $('#job-ticket-' + modal_type + '-body #assign-to').html(assign_to);
            $('#job-ticket-' + modal_type + '-body #ticket-comment').html(comment);
            $('#job-ticket-' + modal_type + '-body #finish-date').html(actual_finish);
            $('#job-ticket-' + modal_type + '-body #ticket-type').html(ticket_type);
            $('#job-ticket-' + modal_type + '-body #ticket-name').html(ticket_name);
            $('#job-ticket-' + modal_type + '-body #ticket-note').html(ticket_note);

            $('#add-note-form #item-id').val(item_id);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });

    $('#modal-job-ticket-' + modal_type).modal();
}

function updateTicketNote() {
    var item_id = parseInt($('#add-note-form #item-id').val());
    var ticket_note = $('#add-note-form #add-note').val();
    var user_name = $('#add-note-form #user-name').val();

    $.ajax({
        type: 'post',
        url: '/tasks/update/' + item_id,
        data: {
            note: ticket_note,
            user_name: user_name,
            tick: Math.random()
        },
        dataType: 'json',
        success: function (data) {
            var today = new Date();
            $('#ticket-note').prepend(user_name + ' ' + today.toLocaleString('en-US') + '<br/>' + ticket_note + '<br/><br/>');
            $('#add-note-form #add-note').val('');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus + ': ' + errorThrown);
        }
    });
}