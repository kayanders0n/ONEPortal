function LoadDashboard(dashboard) {
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'html',
        url: '/dashboard?dash=' + dashboard + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $('#tab-' + dashboard).html(data);
            window.history.pushState('', '', '?dash=' + dashboard);
        },
        error: function (handle, status, error) {
            console.log('LoadDashboard ' + dashboard + ': ' + error + ' ' + status);
        }
    })
}