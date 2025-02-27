<div class="box">
  <div class="box-header with-border">
    <h3 id="db-connection-title" class="box-title">A/R Open Billing Tasks Summary</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div id="accounting-ar-open-billing-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body no-padding">
    <table id="accounting-ar-open-billing-data" class="table table-striped">
      <thead>
      <tr>
        <th>Code</th>
        <th>Billing Task</th>
        <th>Count</th>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <!--
    <div class="box-footer">
        <div class="pull-right">
            <button class="btn btn-default btn-sm" role="link" onclick="location.href='/hyphen'">More <i class="fas fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
   -->
</div>
<?= includePageScript('widgets', 'accounting-ar-open-billing.js'); ?>

