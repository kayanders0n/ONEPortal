$(document).ready(function () {

    LoadEmployeeList();
    LoadTWWVRData();

    $('#show-date').datepicker();
});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
