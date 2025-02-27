<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Estimator Average Profit Margin</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="estimator-profit-margin-form">
        <select id="company" name="company" onchange="LoadEstimatorProfitMarginChart();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
          <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door and Trim</option>

        </select>
          <select id="project-site" name="project_site" onchange="LoadEstimatorProfitMarginChart();">
              <option value="ALL" selected>All</option>
              <option value="EASTSIDE">Eastside</option>
              <option value="WESTSIDE">Westside</option>
              <option value="TUCSON">Tucson</option>
          </select>

      </form>
    </div>
    <div id="estimator-profit-margin-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="estimator-profit-margin-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
    </div>
  </div>
</div>
<?= includePageScript('widgets', 'estimator-profit-margin-chart.js'); ?>

