$(document).ready(function () {
    // need this to be the first element on the page
    $('body').prepend('<div style="width: 100%;" id="barcode-reader"></div>');
    barCodeScanner = new Html5Qrcode('barcode-reader');
});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});
