<?= (!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'job_documents.js') : ''); ?>

<div class="main">
  <h2 class="page-header">Job Info - <?= $data['page']['job_num'] ?></h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-4">
        <form>
          <input type="hidden" id="job-id" name="job_id" value="<?= $data['page']['job_id'] ?>"/>
          <input type="hidden" id="jobsite-id" name="jobsite_id" value=""/>
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
      <div class="col-md-12">
        <div style="margin-top: 15px;"><h3>Documents</h3></div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-lg-12">
            <table id="job-documents-data" class="table table-striped table-hover table-condensed data-table">
              <thead>
              <tr>
                <th>Document</th>
                <th>Date</th>
                <th>Type</th>
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
</div>
