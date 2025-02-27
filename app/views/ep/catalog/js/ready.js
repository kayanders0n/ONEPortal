$(document).ready(function () {

    loadCatalogCategoryList();

    $('#modal-catalog-details').on('shown.bs.modal', function () {
        $('#form-catalog-details #add-material-upc').focus();
    });
});

$(document).ajaxStart(function() {
	$('#ajax-progress').show();
});

$(document).ajaxStop(function() {
	$('#ajax-progress').hide();
});

