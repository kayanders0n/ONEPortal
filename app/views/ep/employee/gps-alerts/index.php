<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'alert_data.js') : '');?>

<div class="main">
  <h2 class="page-header">GPS Employee Alerts</h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-10">
        <div class="pull-left">
          <form class="form-inline" method="post" name="form_hyphen_data" id="form-hyphen-data">
            <div class="form-group">
              <label for="alert-type">Data Type: </label>
              <select class="form-control" name="alert_type" id="alert-type" onchange="LoadAlertData();">
                <option value="POSTED">Posted Speed</option>
                <option value="IDLE TIME">Idle Time</option>
                <?php if ($data['page']['has_token']) { ?>
                <option value="OUT OF RANGE">Out of Range</option>
                <option value="POWER LOST">Power Lost</option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="company-id">Company: </label>
              <select class="form-control" name="company_id" id="company-id" onchange="LoadAlertData();">
                <option value="">All</option>
                <option value="5633">Plumbing</option>
                <option value="21440">Concrete</option>
                <option value="21442">Framing</option>
              </select>
            </div>
            <div class="form-group">
              <label for="show-date-start">Start Date: </label>
              <input class="form-control" type="text" name="show_date_start" id="show-date-start" value="<?=date('m/d/Y', strtotime('yesterday'))?>" size="10" date-format="mm/dd/yyyy" onchange="LoadAlertData();" />
              <label for="show-date-end">End Date: </label>
              <input class="form-control" type="text" name="show_date_end" id="show-date-end" value="<?=date('m/d/Y')?>" size="10" date-format="mm/dd/yyyy" onchange="LoadAlertData();" />
            </div>
            <div class="form-group">
              <label for="company-site">Site: </label>
              <select class="form-control" name="company_site" id="company-site" onchange="LoadAlertData();">
                <option value="">All</option>
                <option value="00010">Mesa</option>
                <option value="00040">East</option>
                <option value="00030">West</option>
                <option value="00020">Tucson</option>
              </select>
            </div>
            <div class="form-group">
              <label for="company-site">Department: </label>
              <select class="form-control" name="company_department" id="company-department" onchange="LoadAlertData();">
                <option value="">All</option>
                <option value="00150">Production</option>
                <option value="00100">Field Managers</option>
                <option value="00155">Warranty</option>
                <option value="00175">Service</option>
              </select>
            </div>
            <div class="form-group">
              <label for="employee-num">Employee#: </label>
              <input class="form-control" type="text" name="employee_num" id="employee-num" value="" pattern="[0-9]" title="Employee#" size="5" onchange="LoadAlertData();" />
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit" onclick="LoadAlertData(); return false;">Refresh</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-2">
        <div class="pull-right">

        </div>
      </div>
    </div>
  </div>
  <br />
  <form>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="loader">
          <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
        </div>
        <table id="alert-data" class="table table-striped table-hover table-condensed data-table hidden">
          <thead>
          <tr>
            <th>Veh#</th>
            <th>Vehicle</th>
            <th>Emp#</th>
            <th>Employee</th>
            <th>Condition</th>
            <th>Time</th>
            <th>Location</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </form>
</div>
