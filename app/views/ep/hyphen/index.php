<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'hyphen_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
  <h2 class="page-header">Process Hyphen Orders</h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-10">
        <div class="pull-left">
          <form class="form-inline" method="post" name="form_hyphen_data" id="form-hyphen-data">
            <div class="form-group">
              <label for="type-id">Data Type: </label>
              <select class="form-control" name="type_id" id="type-id" onchange="LoadHyphenData();">
                <option value="1">Schedule Updates</option>
                  <?php if ($data['page']['has_token']) { ?>
                    <option value="2">Documents</option>
                  <?php } ?>
              </select>
            </div>
              <?php if ($data['page']['has_token']) { ?>
                <div class="form-group">
                  <label for="show-data">Show: </label>
                  <select class="form-control" name="show_data" id="show-data" onchange="LoadHyphenData();">
                    <option value="0">Queued</option>
                    <option value="1">Processed</option>
                    <option value="2">Deleted</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="show-date">Date: </label>
                  <input class="form-control" type="text" name="show_date" id="show-date" value="<?=date('m/d/Y')?>" size="10" date-format="mm/dd/yyyy" onchange="LoadHyphenData();" />
                </div>
              <?php } ?>
            <div class="form-group">
              <button class="btn btn-primary" type="submit" onclick="LoadHyphenData(); return false;">Refresh</button>
            </div>
          </form>
        </div>
      </div>
      <div class="col-md-2">
        <div class="pull-right">
          <button class="btn btn-info" onclick="ReProcessHyphen(); return false;">Re-Process Queued</button>
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
        <table id="hyphen-data" class="table table-striped table-hover table-condensed data-table hidden">
          <thead>
          <tr>
            <th>Code</th>
            <th>Community</th>
            <th>Action</th>
            <th>Address</th>
            <th>Lot</th>
            <th>Scheduled</th>
            <th>Created/Updated</th>
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
