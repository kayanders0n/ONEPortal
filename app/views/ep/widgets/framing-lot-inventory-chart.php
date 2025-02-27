<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Framing Lot Inventory</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div id="framing-lot-inventory-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body no-padding">
    <table id="framing-lot-inventory-data" class="table table-striped table-sm, width:100%">
      <thead>
      <tr>
        <th style="width: 61%">Builder</th>
        <th style="width: 13%">Total Lots</th>
        <th style="width: 13%">Total Jobs</th>
        <th style="width: 13%">Lots Remaining</th>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<?= includePageScript('widgets', 'framing-lot-inventory-chart.js'); ?>

