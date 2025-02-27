<style>
  .dash-ind-num {
    text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
  }
</style>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">ProphetDOC Image Processing</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style="padding-top: 20px;">
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-primary">
        <div class="inner text-center">
          <p>Starts</p>
          <h3 id="job-starts-expired-publish-num" class="dash-ind-num" title="Job Starts ready to publish longer than 4 hours">0</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-primary">
        <div class="inner text-center">
          <p>Other</p>
          <h3 id="pdoc-file-other-num" class="dash-ind-num" title="Files waiting for more than one day">0</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-primary">
        <div class="inner text-center">
          <p>Payroll</p>
          <h3 id="pdoc-file-payroll-num" class="dash-ind-num" title="Files older than 5 days">0</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-primary">
        <div class="inner text-center">
          <p>Pay Index</p>
          <h3 id="pdoc-file-payroll-index-num" class="dash-ind-num" title="Files older than 12 hours">0</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-primary">
        <div class="inner text-center">
          <p>Pay Review</p>
          <h3 id="pdoc-file-payroll-review-num" class="dash-ind-num" title="Files older than 10 days">0</h3>
        </div>
      </div>
    </div>
  </div>
</div>
<?=(config('env') != 'local' ? includePageScript('widgets', 'pdoc-processing.js') : ''); ?>
