<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Estimator Starts Productivity</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="estimator-starts-productivity-form">
        <select id="company" name="company" onchange="LoadEstimatorStartsProductivityChart();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
        </select>
        <select id="days-old" name="days_old" onchange="LoadEstimatorStartsProductivityChart();">
          <option value="0">Today</option>
          <option value="7">7 days</option>
          <option value="14" selected>14 days</option>
          <option value="30">30 days</option>
          <option value="60">60 days</option>
          <option value="90">90 days</option>
        </select>
      </form>
    </div>
    <div id="estimator-starts-productivity-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="estimator-starts-productivity-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
    </div>
  </div>
</div>
<?= includePageScript('widgets', 'estimator-starts-productivity-chart.js'); ?>

