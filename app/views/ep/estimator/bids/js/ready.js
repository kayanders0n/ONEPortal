$(document).ready(function () {

   $('#date-start').datepicker();
   $('#date-end').datepicker();

    loadBidsData();
});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});
