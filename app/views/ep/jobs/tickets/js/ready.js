$(document).ready(function () {

	loadJobTicketsData();

});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
