function loadJobDetailPanels(panel_name) {
    var name = panel_name.split('-');
    switch (panel_name) {
        case 'panel-workers': loadJobPanelWorkers(); break;
        case 'panel-labor': loadJobPanelLabor(); break;
        case 'panel-options': loadJobPanelOptions(); break;
        case 'panel-qa': loadJobPanelQA(); break;

    }
}


function loadJobPanelWorkers() {
    var job_id = parseInt($('#job-id').val());

    $('#job-detail-accordion #workers table tbody').empty();
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/workers/list?job_id=' + job_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $.each(data.results, function (key, item) {

                var $item = item.worker;
                $('#job-detail-accordion #workers table tbody').append('<tr><td>' + $item.name + '</td><td>' + $item.phase + '</td></tr>');

            });
        },
        error: function (handle, status, error) {
            console.log('loadJobPanelWorkers: ' + error + ' ' + status);
        }
    });
}

function loadJobPanelLabor() {
    var job_id = parseInt($('#job-id').val());
    var company_id = parseInt($('#job-company-id').val());

    var row_header = '';
    switch (company_id) {
        case 5633:
            row_header  = '<tr class="text-right" style="color: red; font-style: italic; font-size: 0.95em;"><td colspan="99">Do NOT share BUDGET amounts!</td></tr>';
            row_header += '<tr><th>Phase</th><th class="text-right">Worker</th><th class="text-right">Budget</th><th class="text-right">Actual</th><th class="text-right">Delta</th></tr>';
            break;
        case 21440:
            row_header = '<tr><th>Name</th><th class="text-right">Budget</th></tr>';
            break;
        case 21442:
            row_header = '<tr><th>Name</th><th class="text-right">Budget</th><th class="text-right">Actual</th><th class="text-right">Delta</th></tr>';
            break;
    }

    $('#job-detail-accordion #labor table thead').empty();
    $('#job-detail-accordion #labor table thead').append(row_header);

    $('#job-detail-accordion #labor table tbody').empty();
    $('#job-detail-accordion #labor table tfoot').empty();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/labor/list?job_id=' + job_id +
             '&company_id=' + company_id +
             '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $.each(data.results, function (key, item) {

                var $item = item.labor;

                if ($item.index != 'TOTAL') {
                    var row_data = '';
                    switch (company_id) {
                        case 5633:
                            row_data = '<tr><td>' + $item.name + '</td>' +
                                '<td class="text-right" style="' + $item.flex.style + '">' + $item.flex.formatted + '</td>' +
                                '<td class="text-right danger" style="' + $item.budget.style + '" title="Do NOT share budget">' + $item.budget.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.actual.style + '">' + $item.actual.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.delta.style + '">' + $item.delta.formatted + '</td></tr>';
                            $('#job-detail-accordion #labor table tbody').append(row_data);

                            if ($item.other != undefined) {
                                $.each($item.other, function (okey, oitem) {
                                    if (oitem.budget.amount != 0) {
                                        row_data = '<tr style="font-style: italic; font-size: 0.95em;"><td>&nbsp;</td>' +
                                            '<td class="text-right">' + oitem.budget.formatted + '</td>' +
                                            '<td colspan="99">' + oitem.name + '</td></tr>';
                                        $('#job-detail-accordion #labor table tbody').append(row_data);
                                    }
                                });
                            }
                            break;
                        case 21440:
                            row_data = '<tr><td>' + $item.name + '</td>' +
                                '<td class="text-right" style="' + $item.budget.style + '">' + $item.budget.formatted + ' ' + $item.uom + '</td></tr>';
                            $('#job-detail-accordion #labor table tbody').append(row_data);
                            break;
                        case 21442:
                            row_data = '<tr><td>' + $item.name + '</td>' +
                                '<td class="text-right" style="' + $item.budget.style + '">' + $item.budget.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.actual.style + '">' + $item.actual.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.delta.style + '">' + $item.delta.formatted + '</td></tr>';
                            $('#job-detail-accordion #labor table tbody').append(row_data);
                            break;
                    }
                } else {
                    // total row
                    var row_data = '';
                    switch (company_id) {
                        case 5633:
                            row_data = '<tr><td class="text-right" style="font-weight: bold;" colspan="2">TOTAL:</td>' +
                                '<td class="text-right" style="' + $item.budget.style + '">' + $item.budget.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.actual.style + '">' + $item.actual.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.delta.style + '">' + $item.delta.formatted + '</td></tr>';
                            break;
                        case 21440:
                            break;
                        case 21442:
                            row_data = '<tr><td class="text-right" style="font-weight: bold;" >TOTAL:</td>' +
                                '<td class="text-right" style="' + $item.budget.style + '">' + $item.budget.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.actual.style + '">' + $item.actual.formatted + '</td>' +
                                '<td class="text-right" style="' + $item.delta.style + '">' + $item.delta.formatted + '</td></tr>';
                            break;
                    }

                    if (row_data != '') {
                        $('#job-detail-accordion #labor table tfoot').append(row_data);
                    }
                }
            });
        },
        error: function (handle, status, error) {
            console.log('loadJobPanelWorkers: ' + error + ' ' + status);
        }
    });
}


function loadJobPanelOptions() {
    var job_id = parseInt($('#job-id').val());

    $('#job-detail-accordion #options table tbody').empty();
    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/options/list?job_id=' + job_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            $.each(data.results, function (key, item) {

                var $item = item.option;

                var row_option = '<tr style="' + $item.style_color + '">';
                row_option += '<td>' + $item.code + '</td>';
                row_option += '<td>' + $item.name;
                if ($item.units > 1) {
                    row_option += ' <span style="font-weight: bold; color: red;">(' + $item.units.toFixed(2) + ' units)</span>';
                }
                if ($item.location != '') {
                    row_option += ' <span style="font-weight: bold; color:#FF8C00;">(' + $item.location + ')</span>';
                }
                if ($item.note != '') {
                    row_option += '<br /><span style="font-size: 0.9em; color: #FF6347;">' + $item.note + '</span>';
                }
                row_option += '</td>';
                row_option += '<td>' + $item.activity_date + '</td></tr>';
                $('#job-detail-accordion #options table tbody').append(row_option);

            });
        },
        error: function (handle, status, error) {
            console.log('loadJobPanelWorkers: ' + error + ' ' + status);
        }
    });
}

function loadJobTakeoff() {

    var job_id = parseInt($('#job-id').val());
    var company_id = parseInt($('#job-company-id').val());

    var tabs = $('#takeoff-tabs');
    $(tabs).empty();

    var tabs_content = $('#takeoff-tabs-content');
    $(tabs_content).empty();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/takeoff/list?job_id=' + job_id + '&company_id=' + company_id + '&tick=' + Math.random(),
        success: function (data, status, handle) {
            // setup tabs
            $.each(data.results, function (key, item) {
                var $phase = item.phase;

                var tab_line = '';
                if (key==0) { // first one is always active
                    tab_line += '<li class="active">';
                } else {
                    tab_line += '<li>';
                }
                tab_line += '<a data-toggle="tab" data-target="#' + $phase.name_java + '">' + $phase.name + '</a></li>';
                $(tabs).append(tab_line);

            });

            // setup tabs content
            $.each(data.results, function (key, item) {
                var $phase = item.phase;

                var content_line = '';

                if (key==0) { // first one is always active
                    content_line += '<div id="' + $phase.name_java + '" class="tab-pane fade in active">';
                } else {
                    content_line += '<div id="' + $phase.name_java + '" class="tab-pane fade">';
                }

                content_line += '<div class="panel-group" id="takeoff-' + $phase.name_java + '-accordion">';

                $.each($phase.locations, function (lkey, litem) {
                    var $location = litem.location;

                    content_line += '<div class="panel panel-warning">';
                    content_line += '<div class="panel-heading panel-toggle" data-parent="#takeoff-' + $phase.name_java + '-accordion" data-toggle="collapse" data-target="#takeoff-' + $phase.name_java + '-' + $location.name_java + '">';

                    content_line += '<h4 class="panel-title"><span><i class="fa fa-arrow-right fa-sm fa-fw"></i> ' + $location.name + '</span></h4>';
                    content_line += '</div>';

                    content_line += '<div id="takeoff-' + $phase.name_java + '-' + $location.name_java + '"';
                    if ((lkey == 0) && (company_id == 21440)) { // auto expand the first one for concrete
                        content_line += ' class="panel-collapse collapse in" ';
                    } else {
                        content_line += ' class="panel-collapse collapse" ';
                    }
                    content_line += '>';

                    content_line += '<div class="panel-body">';
                    content_line += '<table class="table table-striped table-responsive table-hover table-condensed">';
                    content_line += '<thead><tr><th>Description</th><th>Item</th><th class="text-right">Units</th></tr></thead>';
                    content_line += '<tbody>';

                    //details go here

                    $.each($location.items, function (ikey, iitem) {
                        var $item = iitem.item;

                        var whs_info = '';
                        var doc_link = '';

                        if ($item.whs_status == 'SHIPPED') {
                            whs_info = '<span style="color: red;">SHIPPED</span>';
                        } else if ($item.whs_status == '100% COMPLETE') {
                            whs_info = '<span style="color: red;">100% COMPLETE</span>';
                        }

                        if ($item.doc_count > 0) {
                            doc_link = '<i class="fa fa-external-link-alt fa-lg fa-fw"></i>'
                        }

                        content_line += '<tr>';
                        content_line += '<td>' + $item.additional + '</td>';
                        content_line += '<td>' + '(' + $item.code + ') ' + $item.name + ' ' + whs_info + ' ' + doc_link + '</td>';
                        content_line += '<td class="text-right">' + $item.units.formatted + '</td>';
                        content_line += '</tr>';
                    });

                    if ((company_id == 21440) && ($location.concrete_yards.amount != 0)) { // concrete total
                        var $concrete = $location.concrete_yards;
                        content_line += '<tr>';
                        content_line += '<td>&nbsp;</td>';
                        content_line += '<td class="text-right" style="font-size: 1.25em;"><strong>TOTAL CONCRETE YARDAGE:</strong></td>';
                        content_line += '<td class="text-right" style="font-size: 1.25em;"><strong>' + $concrete.formatted + '</strong></td>';
                        content_line += '</tr>';
                    }

                    content_line += '</tbody>';
                    content_line += '</table>';
                    content_line += '</div>';
                    content_line += '</div>';
                    content_line += '</div>';

                });

                content_line += '</div>';
                content_line += '</div>';

                $(tabs_content).append(content_line);

            });

        },
        error: function (handle, status, error) {
            console.log('loadJobTakeoff: ' + error + ' ' + status);
        }
    });

}