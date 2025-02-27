function loadWorkOrderList() {
    console.log('Loading Work Order List...');
    var job_num = parseInt($('#job-num').val()) || 0;
    if (job_num == 0) {
        console.log('No Job...');
        return;
    }
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/wo/list?job_num=' + job_num + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#work-order-list').empty();
            $('#work-order-list').append('<option value="0" selected>-- Select Work Order --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.wo;
                $('#work-order-list').append('<option value="' + $item.num + '">' + $item.name + '</option>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadWorkOrderList: ' + error + ' ' + status);
        }
    });
}

function setWorkOrderNum() {
    var wo_num = $('#work-order-list').val();
    if (wo_num) {
        $('#wo-num').val(wo_num);
    } else {
        $('#wo-num').val('');
    }
}


function loadCommunityList() {
    var builder_id = $('#builder-id').val();
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/community/list?builder_id=' + builder_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#community-id').empty();
            $('#community-id').append('<option value="0" selected>-- Select Community --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.community;
                $('#community-id').append('<option value="' + $item.item_id + '">' + $item.name + '</option>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadCommunityList: ' + error + ' ' + status);
        }
    });
}

function loadLotList() {
    var community_id = $('#community-id').val();
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/lot/list?community_id=' + community_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#job-num-find').empty();
            $('#job-num-find').append('<option value="0" selected>-- Select Lot --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.lot;
                $('#job-num-find').append('<option value="' + $item.num + '">' + $item.code + ' [' + $item.company + '] ' + $item.start_date + '</option>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadCommunityList: ' + error + ' ' + status);
        }
    });
}

function selectJob() {
    var job_num = $('#job-num-find').val();

    $('#job-num').val(job_num);
    $('#form-jobs-find').submit();
}