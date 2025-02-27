function LoadNetworkPingData() {

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/network/ping/list?tick=' + Math.random(),
        success: function (data, status, handle) {
            var pingDate = new Date();
            $('#network-ping-time').html(' ' + pingDate.toLocaleString('en-US'));

            $.each(data.results, function (key, item) {

                var network_name = item.network.name;
                var status = item.network.status;
                var time = item.network.time;
                var uom = item.network.uom;

                $('#' + network_name.toLowerCase() + '-ping-box').removeClass('bg-red');
                $('#' + network_name.toLowerCase() + '-ping-box').removeClass('bg-primary');

                if (status=='success') {
                    $('#' + network_name.toLowerCase() + '-ping-box').addClass('bg-primary');
                    $('#' + network_name.toLowerCase() + '-ping-time').html(time+uom);
                    if (time > 30) {
                        $('#' + network_name.toLowerCase() + '-ping-time').css('color', 'Red');
                    } else if (time > 15) {
                        $('#' + network_name.toLowerCase() + '-ping-time').css('color', 'Yellow');
                    } else {
                        $('#' + network_name.toLowerCase() + '-ping-time').css('color', 'White');
                    }
                } else {
                    $('#' + network_name.toLowerCase() + '-ping-box').addClass('bg-red');
                    $('#' + network_name.toLowerCase() + '-ping-time').html('Down');
                    $('#' + network_name.toLowerCase() + '-ping-time').css('color', 'Black');
                }

            });
        },
        error: function (handle, status, error) {
            console.log('LoadNetworkPingData: ' + error + ' ' + status);
        }
    })
}

$(document).ready(function () {
    LoadNetworkPingData();
});

$(document).ajaxStart(function () {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function () {
    $('#ajax-progress').hide();
});
