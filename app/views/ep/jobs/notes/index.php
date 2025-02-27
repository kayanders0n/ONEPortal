<?= (!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'job_notes.js') : ''); ?>

<div class="main">
  <h2 class="page-header">Job Info - <?= $data['page']['job_num'] ?></h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-4">
        <form>
          <input type="hidden" id="job-id" name="job_id" value="<?= $data['page']['job_id'] ?>"/>
          <input type="hidden" id="job-company-id" name="job_company_id" value="0"/>
        </form>
        <div class="panel panel-success">
          <div class="panel-heading">
            <h4 class="panel-title">
              <span><i class="fas fa-truck fa-fw"></i> Job Info</span>
            </h4>
          </div>
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
          </table>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-success">
          <div class="panel-heading">
            <h4 class="panel-title">
              <span><i class="fa fa-sticky-note fa-lg fa-fw"></i> Notes</span>
            </h4>
          </div>
          <div style="padding: 10px;">
            <div id="job-note"></div>
            <form name="add_note_form" id="add-note-form" method="post" action="">
              <h4>Add Note:</h4>
              <input type="hidden" id="job-id" name="job_id" value="<?= $data['page']['job_id'] ?>"/>
              <input type="hidden" name="user_name" id="user-name" value="<?= $data['user']['user_name']; ?>">
              <textarea class="form-control" name="add_note" id="add-note" cols="50" rows="3"></textarea>
              <button class="btn btn-primary" type="button" style="margin-top: 5px;" onclick="updateJobNote(); return false;">Add Note</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="panel panel-success" id="incident-report-panel" style="display: none;">
          <div class="panel-heading">
            <h4 class="panel-title">
              <span><i class="fas fa-cloud"></i> Incident Report:</span>
            </h4>
          </div>
          <div style="padding: 10px;">
            <form name="incident_report_form" id="incident-report-form" method="post" action="">
              <input type="hidden" id="job-id" name="job_id" value="<?= $data['page']['job_id'] ?>"/>
              <input type="hidden" name="user_name" id="user-name" value="<?= $data['user']['user_name']; ?>">
              <div class="form-group">
                <label for="weather_note">Weather</label>
                <textarea class="form-control" name="weather_note" id="weather-note" cols="50" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label for="material_note">Material/Service</label>
                <textarea class="form-control" name="material_note" id="material-note" cols="50" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label for="pump_note">Pump:</label>
                <textarea class="form-control" name="pump_note" id="pump-note" cols="50" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label for="supplier_note">Supplier Comment:</label>
                <textarea class="form-control" name="supplier_note" id="supplier-note" cols="50" rows="3"></textarea>
              </div>
              <button class="btn btn-primary" type="button" onclick="updateIncidentReport(); return false;">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
