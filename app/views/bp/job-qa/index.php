<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'job_qa_data.js') : '');?>

<div class="main">
  <h2 class="page-header"><?=$data['page']['title'];?></h2>
  <form>
    <input type="hidden" name="builder_id" id="builder-id" value="<?=$data['user']['seq_id'];?>">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="loader">
          <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
        </div>
        <table id="job-qa-data" class="table table-striped table-hover table-condensed data-table hidden">
          <thead>
          <tr>
            <th>QA #</th>
            <th>Community</th>
            <th>Lot</th>
            <th>Address</th>
            <th>Type</th>
            <th>Submitted</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </form>
</div>
