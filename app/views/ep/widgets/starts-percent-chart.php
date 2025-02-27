<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Starts Percent Processed</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="starts-percent-form">
        <select id="type" name="type" onchange="LoadStartsPercentChart();">
          <option value="ALL" selected>All</option>
          <option value="STARTS" selected>Starts</option>
          <option value="CHANGES">Changes</option>
        </select>
      </form>
    </div>
    <div id="starts-percent-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="starts-percent-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
    </div>
  </div>
</div>
<?= includePageScript('widgets', 'starts-common.js'); ?>
<?= includePageScript('widgets', 'starts-percent-chart.js'); ?>


