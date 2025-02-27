<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'find.js') : '');?>

<div class="main">
    <h2 class="page-header">Find Job</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <?php if (isset($data['params']['error'])) { echo '<h3 style="color: red;">', $data['params']['error'], '</h3>'; }  ?>
                    <form class="form-inline" method="post" action="jobs/lookup" name="form_jobs_find" id="form-jobs-find">
                      <div class="form-group">
                          <label for="job-num">Job#: </label>
                          <input type="number" class="form-control" name="job_num" id="job-num" value="" />
                      </div>
                      <strong>- or -</strong>
                      <div style="clear:both; padding: 10px;"></div>
                      <div class="form-group">
                          <label for="builder-id">Builder: </label>
                          <select class="form-control" name="builder_id" id="builder-id" onchange="loadCommunityList();">
                              <option value="0" selected>-- Select Builder --</option>
                          </select>
                      </div>
                      <div class="form-group">
                        <label for="community-id">Community: </label>
                        <select class="form-control" name="community_id" id="community-id" onchange="loadLotList();">
                          <option value="0" selected>-- Select Community --</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="job-num-find">Lot: </label>
                        <select class="form-control" name="job_num_find" id="job-num-find" onchange="selectJob();">
                          <option value="0" selected>-- Select Lot --</option>
                        </select>
                      </div>
                      <div style="clear:both; padding:10px;"></div>
                      <div class="form-group">
                          <button class="btn btn-primary" type="submit" >Find Job</button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
