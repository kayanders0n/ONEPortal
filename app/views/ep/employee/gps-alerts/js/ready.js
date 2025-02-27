$(document).ready(function () {

    LoadAlertData();

    $('#show-date-start').datepicker();
    $('#show-date-end').datepicker();
});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
