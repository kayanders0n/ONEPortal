<?php
if (!isset($loc_default)) { $loc_default = ''; }
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Schedule Phases Completed</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="sched-phase-completed-form">
        <select id="site" name="site" onchange="LoadSchedPhaseCompletedChart();">
          <option value="ALL" selected>All Sites</option>
          <option value="PHX" <?=($loc_default=='PHX')?'selected':'';?>>Phoenix</option>
          <option value="TUC" <?=($loc_default=='TUC')?'selected':'';?>>Tucson</option>
        </select>
        <select id="company" name="company" onchange="LoadSchedPhaseCompletedChart();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
        </select>
        <select id="days-old" name="days-old" onchange="LoadSchedPhaseCompletedChart();">
          <option value="7">7 days</option>
          <option value="14" selected>14 days</option>
          <option value="30">30 days</option>
          <option value="60">60 days</option>
          <option value="90">90 days</option>
        </select>
      </form>
    </div>
    <div id="sched-phase-completed-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="sched-phase-completed-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
    </div>
  </div>
</div>
<?= includePageScript('widgets', 'sched-phase-completed-chart.js'); ?>

