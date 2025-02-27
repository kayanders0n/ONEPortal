$(document).ready(function () {

    loadJobData();

    $( "#job-detail-accordion .panel" ).on( "show.bs.collapse", function( event ) {
        loadJobDetailPanels(event.currentTarget.id);
    });


});

$(document).ajaxStart(function() {
    $('#ajax-progress').show();
});

$(document).ajaxStop(function() {
    $('#ajax-progress').hide();
});