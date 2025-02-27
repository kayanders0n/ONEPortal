<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'job_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'job_details.js') : '');?>

<style>
  .concrete-data { display: none; }

  .tab_custom > li.active > a {
    background-color: #d6e9c6;
  }
</style>
<div class="main">
    <h2 class="page-header">Job Info - <?=$data['page']['job_num']?></h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-4">
                <form>
                    <input type="hidden" id="job-id" name="job_id" value="<?=$data['page']['job_id']?>" />
                    <input type="hidden" id="job-company-id" name="job_company_id" value="0" />
                </form>
                <table class="table table-striped table-responsive table-condensed table-hover no-margin">
                  <tr>
                    <td class="text-nowrap"><strong>Builder:</strong></td>
                    <td><span id="builder-name">Builder Name</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Project:</strong></td>
                    <td><span id="project-name">Project Name</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Lot:</strong></td>
                    <td><span id="lot-num">Lot Number</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Plan:</strong></td>
                    <td><span id="plan-code">Plan Code</span> <span id="plan-elevation">Elevation</span> <span id="job-house-hand">House Hand</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Started:</strong></td>
                    <td><span id="job-start-date">Job Start Date</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Address:</strong></td>
                    <td><span id="jobsite-address-link">Jobsite Full Address and Link Goes Here</span></td>
                  </tr>
                  <tr id="bluestake-row">
                    <td class="text-nowrap"><strong>Bluestake:</strong></td>
                    <td><span id="bluestake">Bluestake Number Goes Here</span></td>
                  </tr>
                  <tr id="coe-date-row">
                    <td class="text-nowrap"><strong>C.O.E Date:</strong></td>
                    <td><span id="coe-date">COE Date</span></td>
                  </tr>
                  <tr id="completed-date-row">
                    <td class="text-nowrap"><strong>Completed:</strong></td>
                    <td><span id="completed-date">Completed On Goes Here</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Estimator:</strong></td>
                    <td><span id="estimator-name">Estimator Name</span> <a id="estimator-email" href="">Estimator Email</a></td>
                  </tr>
                  <tr class="concrete-data" style="background-color: bisque;">
                    <td colspan="99"><span style="color: green"><strong>CONCRETE INFO</strong></span></td>
                  </tr>
                  <tr class="concrete-data">
                    <td class="text-nowrap"><strong>Concrete:</strong></td>
                    <td><span id="concrete-vendor">Concrete Vendor</span> <strong>Mix:</strong> <span id="concrete-mix">Mix Code</span></td>
                  </tr>
                  <tr id="abc-vendor-row" class="concrete-data">
                    <td class="text-nowrap"><strong>ABC Vendor:</strong></td>
                    <td><span id="abc-vendor">ABC Vendor</span> <strong>Tons:</strong> <span id="abc-units">ABC Total Tons</span> <a id="abc-email" href=""><i class="fa fa-envelope fa-lg fa-fw"></i> Email ABC</a></td>
                  </tr>
                  <tr id="cable-vendor-row" class="concrete-data">
                    <td class="text-nowrap"><strong>PT Cables:</strong></td>
                    <td><span id="cable-vendor">Cable Vendor</span> <strong>Feet:</strong> <span id="cable-units">Cable Feet</span> <a id="cable-email" href=""><i class="fa fa-envelope fa-lg fa-fw"></i> Email PT Cables</a></td>
                  </tr>
                  <tr class="concrete-data">
                    <td class="text-nowrap"><strong>Pump:</strong></td>
                    <td><span id="concrete-pump">Concrete Pump</span> <a id="pump-email" href=""><i class="fa fa-envelope fa-lg fa-fw"></i> Email Pump</a></td>
                  </tr>
                  <tr class="concrete-data">
                    <td class="text-nowrap"><strong>Inspection:</strong></td>
                    <td><span id="concrete-inspection">Concrete Inspection</span> <a id="inspection-email" href=""><i class="fa fa-envelope fa-lg fa-fw"></i> Email Inspection</a></td>
                  </tr>
                  <tr class="concrete-data">
                    <td class="text-nowrap"><strong>Pre-Treat:</strong></td>
                    <td><span id="concrete-pretreat">Concrete Pre-Treat</span> <a id="pretreat-email" href=""><i class="fa fa-envelope fa-lg fa-fw"></i> Email Pre-Treat</a></td>
                  </tr>
                </table>
            </div>

            <div class="col-md-6">
              <div class="panel-group" id="job-detail-accordion">
                <div class="panel panel-success" id="panel-workers">
                  <div class="panel-heading panel-toggle" data-parent="#job-detail-accordion" data-toggle="collapse" data-target="#workers">
                    <h4 class="panel-title">
                      <span><i class="fa fa-users fa-lg fa-fw"></i> Workers</span>
                    </h4>
                  </div>
                  <div id="workers" class="panel-collapse collapse">
                    <div class="panel-body">
                      <table class="table table-striped table-responsive table-hover table-condensed">
                        <thead>
                        <tr>
                          <th>Worker</th>
                          <th>Phase</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="panel panel-success" id="panel-labor">
                  <div class="panel-heading panel-toggle" data-parent="#job-detail-accordion" data-toggle="collapse" data-target="#labor">
                    <h4 class="panel-title">
                      <span><i class="fa fa-dollar-sign fa-lg fa-fw"></i> Labor</span>
                    </h4>
                  </div>
                  <div id="labor" class="panel-collapse collapse">
                    <div class="panel-body">
                      <table class="table table-striped table-responsive table-hover table-condensed">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot style="background-color:#FAFAD2;">
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="panel panel-success" id="panel-options">
                  <div class="panel-heading panel-toggle" data-parent="#job-detail-accordion" data-toggle="collapse" data-target="#options">
                    <h4 class="panel-title">
                      <span><i class="fa fa-list fa-lg fa-fw"></i> Options</span>
                    </h4>
                  </div>
                  <div id="options" class="panel-collapse collapse">
                    <div class="panel-body">
                      <table class="table table-striped table-responsive table-hover table-condensed">
                        <thead>
                        <tr>
                          <th>Option#</th>
                          <th>Description</th>
                          <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div style="margin-top: 15px;"><h3>Takeoff</h3></div>
              <ul id="takeoff-tabs" class="nav nav-tabs tab_custom">
              </ul>

              <div id="takeoff-tabs-content" class="tab-content">
              </div>
            </div>
        </div>
    </div>
</div>
