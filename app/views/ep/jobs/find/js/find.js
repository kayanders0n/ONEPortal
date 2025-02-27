function loadBuilderList() {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/builder/list?tick=' + Math.random(),
        success: function (data, status, handle) {

            $('#builder-id').empty();
            $('#builder-id').append('<option value="0" selected>-- Select Builder --</option>');

            $.each(data.results, function (key, item) {

                var $item = item.builder;
                $('#builder-id').append('<option value="' + $item.item_id + '">' + $item.name + '</option>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadBuilderList: ' + error + ' ' + status);
        }
    });
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