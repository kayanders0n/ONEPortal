$(document).ready(function () {

});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
