function loadJobData() {
    var job_id = $('#job-id').val();

    $.ajax({
        cache: false,
        type: 'GET',
        dataType: 'json',
        url: '/jobs/show/' + job_id + '?tick=' + Math.random(),
        success: function (data, status, handle) {

            var $item = data.result;

            $('#job-company-id').val($item.company.id);

            $('#builder-name').html($item.builder.name);
            $('#project-name').html($item.project.name);
            $('#lot-num').html($item.jobsite.code);
            $('#plan-code').html($item.plan.code);
            $('#plan-elevation').html($item.plan.elevation);
            $('#job-house-hand').html('<strong>Hand:</strong> ' + $item.job.house_hand);
            $('#job-start-date').html($item.job.start_date);

            // set the map link for the address
            var maps = 'https://maps.google.com/?q=';
            if (navigator.userAgent.match(/(iPhone|iPad)/)) {
                maps = 'https://maps.apple.com/?q=';
            }
            maps = maps + $item.jobsite.address1 + ' ' + $item.jobsite.city + ', ' + $item.jobsite.state;
            $('#jobsite-address-link').html('<a href="' + maps + '" target="_blank">' + $item.jobsite.address1 + '</a>');

            $('#bluestake').html($item.jobsite.bluestake);
            if ($item.jobsite.bluestake) {
                $('#bluestake-row').show();
            } else {
                $('#bluestake-row').hide();
            }

            $('#coe-date').html($item.jobsite.coe_date);
            if ($item.jobsite.coe_date) {
                $('#coe-date-row').show();
            } else {
                $('#coe-date-row').hide();
            }

            $('#completed-date').html($item.job.completed_date);
            if ($item.job.completed_date) {
                $('#completed-date-row').show();
            } else {
                $('#completed-date-row').hide();
            }

            $('#estimator-name').html($item.estimator.name);
            $('#estimator-email').html('<i class="fa fa-envelope fa-lg fa-fw"></i> Email Estimator');
            $('#estimator-email').attr('href', 'mailto:'+$item.estimator.email + '?subject=Job# ' + $item.job.num);


            if ($item.concrete != undefined) {
                $('.concrete-data').show();
                $('#concrete-vendor').html($item.concrete.project.concrete_vendor);
                $('#concrete-mix').html($item.concrete.project.concrete_mix_code);

                if ($item.concrete.abc.vendor) {
                    $('#abc-vendor-row').show();

                    $('#abc-vendor').html($item.concrete.abc.vendor);
                    $('#abc-units').html($item.concrete.abc.units.toFixed(2));

                    var email_body = '';

                    if ($item.concrete.abc.email) {

                        email_body = '';
                        email_body += 'PO%23: ' + $item.concrete.abc.po_num + '%0A';
                        email_body += 'Job:%23: ' + $item.job.num + '%0A';
                        email_body += 'Builder: ' + $item.builder.name + '%0A';
                        email_body += 'Community: ' + $item.project.name + '%0A';
                        email_body += 'Lot%23: ' + $item.jobsite.code + '%0A';
                        email_body += 'Address: ' + $item.jobsite.address1 + '%0A';
                        email_body += 'ABC Tons: ' + $item.concrete.abc.units.toFixed(2) + '%0A';

                        $('#abc-email').attr('href', 'mailto:' + $item.concrete.abc.email + '?subject=ABC%20Delivery%20Request&body=' + email_body);
                    } else {
                        $('#abc-email').html('No email defined');
                    }
                } else {
                    $('#abc-vendor-row').hide();
                }


                if ($item.concrete.cable.vendor) {
                    $('#cable-vendor-row').show();

                    $('#cable-vendor').html($item.concrete.cable.vendor);
                    $('#cable-units').html($item.concrete.cable.units.toFixed(2));

                    if ($item.concrete.cable.email) {

                        email_body = '';
                        email_body += 'PO%23: ' + $item.concrete.cable.po_num + '%0A';
                        email_body += 'Job:%23: ' + $item.job.num + '%0A';
                        email_body += 'Builder: ' + $item.builder.name + '%0A';
                        email_body += 'Community: ' + $item.project.name + '%0A';
                        email_body += 'Lot%23: ' + $item.jobsite.code + '%0A';
                        email_body += 'Address: ' + $item.jobsite.address1 + '%0A';
                        email_body += 'Cable Feet: ' + $item.concrete.cable.units.toFixed(2) + '%0A';

                        $('#cable-email').attr('href', 'mailto:' + $item.concrete.cable.email + '?subject=PT%20Cable%20Request&body=' + email_body);
                    } else {
                        $('#cable-email').html('No email defined');
                    }
                } else {
                    $('#cable-vendor-row').hide();
                }

                email_body = '';
                email_body += 'Job:%23: ' + $item.job.num + '%0A';
                email_body += 'Builder: ' + $item.builder.name + '%0A';
                email_body += 'Community: ' + $item.project.name + '%0A';
                email_body += 'Lot%23: ' + $item.jobsite.code + '%0A';
                email_body += 'Address: ' + $item.jobsite.address1 + '%0A';

                $('#concrete-pump').html($item.concrete.project.pump);
                $('#pump-email').attr('href', 'mailto:?subject=Pump%20Request&body=' + email_body);

                $('#concrete-inspection').html($item.concrete.project.inspection);
                $('#inspection-email').attr('href', 'mailto:?subject=Inspection%20Request&body=' + email_body);

                $('#concrete-pretreat').html($item.concrete.project.pretreat);
                $('#pretreat-email').attr('href', 'mailto:?subject=Pre-Treat%20Request&body=' + email_body);
            } else {
                $('.concrete-data').hide();
            }

            loadJobTakeoff(); // needs to be here to make sure it is after the required data is loaded
        },
        error: function (handle, status, error) {
            console.log('loadBuilderList: ' + error + ' ' + status);
        }
    });
}