$(document).ready(function () {

    LoadHyphenData();

    $('#show-date').datepicker();
});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
