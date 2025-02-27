<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'twwvr_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
  <h2 class="page-header">Process Visual Record Items</h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-10">
        <div class="pull-left">
          <form class="form-inline" method="post" name="form_twwvr_data" id="form-twwvr-data">
            <input type="hidden" name="user_employee_id" id="user-employee-id" value="<?=$data['user']['seq_id'];?>">
            <?php if (($data['page']['admin_user']) || ($data['page']['admin_twwvr'])) : ?>
            <div class="form-group">
              <label for="company-id">Company: </label>
              <select class="form-control" name="company_id" id="company-id" onchange="LoadTWWVRData();">
                <option value="0">All</option>
                <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
                <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
                <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
                <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door and Trim</option>
                <option value="<?=COOLINGHEATING_ENTITYID?>" <?=(COOLINGHEATING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Cooling and Heating</option>
              </select>
            </div>
            <?php else: ?>
              <input type="hidden" name="company_id" id="company-id" value="<?=$data['user']['company_id'];?>">
            <?php endif; ?>
            <div class="form-group">
              <label for="type-id">Data Type: </label>
              <select class="form-control" name="type_id" id="type-id" onchange="LoadTWWVRData();">
                <option value="1">Supers</option>
                <option value="2">Field</option>
                <option value="3" selected>All</option>
              </select>
            </div>
              <?php if (($data['page']['admin_user']) || ($data['page']['admin_twwvr']) || ($data['page']['manager_twwvr'])) : ?>
              <div class="form-group">
                <label for="employee-id">Employee: </label>
                <select class="form-control" name="employee_id" id="employee-id" onchange="LoadTWWVRData();">
                  <option value="0" selected>All</option>
                  <?php if (!$data['page']['admin_user']) : ?>
                  <option value="<?=$data['user']['seq_id'];?>">My Assignments</option>
                  <?php endif; ?>
                </select>
              </div>
              <?php else: ?>
              <input type="hidden" name="employee_id" id="employee-id" value="<?=$data['user']['seq_id'];?>" />
              <?php endif; ?>
              <?php if (($data['page']['admin_user']) || ($data['page']['admin_twwvr']) || ($data['page']['manager_twwvr'])) : ?>
                <div class="form-group">
                  <label for="show-data">Show: </label>
                  <select class="form-control" name="show_data" id="show-data" onchange="LoadTWWVRData();">
                    <option value="0">Queued</option>
                    <option value="1">Processed</option>
                    <option value="2">Deleted</option>
                    <option value="3">Not Linked</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="show-date">Date: </label>
                  <input class="form-control" type="text" name="show_date" id="show-date" value="<?=date('m/d/Y')?>" size="10" date-format="mm/dd/yyyy" onchange="LoadTWWVRData();" />
                </div>
              <?php endif; ?>
              <?php if (($data['page']['admin_user']) || ($data['page']['admin_twwvr'])) : ?>
                <div class="form-group">
                  <label for="show-site">Site: </label>
                  <select class="form-control" name="show_site" id="show-site" onchange="LoadTWWVRData();">
                    <option value="0">All</option>
                    <option value="100">Phoenix</option>
                    <option value="30" <?=(30==$data['user']['site'])?'selected':''?>>Westside</option>
                    <option value="40" <?=(40==$data['user']['site'])?'selected':''?>>Eastside</option>
                    <option value="20" <?=(20==$data['user']['site'])?'selected':''?>>Tucson</option>
                  </select>
                </div>
              <?php else: ?>
              <input type="hidden" name="show_site" id="show-site" value="<?=$data['user']['site'];?>" />
              <?php endif; ?>
            <div class="form-group">
              <button class="btn btn-primary" type="submit" onclick="LoadTWWVRData(); return false;">Refresh</button>
            </div>
          </form>
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
        <table id="twwvr-data" class="table table-striped table-hover table-condensed data-table hidden">
          <thead>
          <tr>
            <th>Type</th>
            <th>Date</th>
            <th>Employee</th>
            <th>Ticket#</th>
            <th>Job#</th>
            <th>Options</th>
            <th>Recorded</th>
            <th>GPS</th>
            <th>Assigned</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </form>
</div>
<?php include 'modals/edit.php'; ?>
<?php include 'modals/view.php'; ?>
<?php include 'modals/process.php'; ?>
