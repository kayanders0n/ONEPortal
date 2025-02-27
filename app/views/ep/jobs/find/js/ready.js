$(document).ready(function () {

    loadBuilderList();

});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
