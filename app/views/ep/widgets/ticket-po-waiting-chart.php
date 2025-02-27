<?php
if (!isset($loc_default)) { $loc_default = ''; }
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Purchase Orders Needing Approval</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="ticket-po-waiting-form">
        <select id="site" name="site" onchange="LoadTicketPOWaitingChart();">
          <option value="ALL" selected>All Sites</option>
          <option value="PHX" <?=($loc_default=='PHX')?'selected':'';?>>Phoenix</option>
          <option value="TUC" <?=($loc_default=='TUC')?'selected':'';?>>Tucson</option>
        </select>
        <select id="company" name="company" onchange="LoadTicketPOWaitingChart();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
        </select>
        <select id="days-old" name="days-old" onchange="LoadTicketPOWaitingChart();">
          <option value="30" selected>30 days</option>
          <option value="60">60 days</option>
          <option value="90">90 days</option>
          <option value="120">120 days</option>
          <option value="180">180 days</option>
        </select>
      </form>
    </div>
    <div id="ticket-po-waiting-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="ticket-po-waiting-chart"></canvas>
    </div>
  </div>
  <div class="box-footer">
    <div class="pull-right">
      <button class="btn btn-default btn-sm" role="link" onclick="GetTicketPOWaitingBuilderData();">Top 10 Builders <i
                class="fas fa-arrow-right fa-fw"></i></button>
    </div>
  </div>
  <div class="box-body no-padding">
    <table id="ticket-po-waiting-builder-data" class="table table-striped hidden">
      <thead>
      <tr>
        <th>ID#</th>
        <th>Builder</th>
        <th>Total Amount</th>
        <th class="text-right">POs</th>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
<?= includePageScript('widgets', 'ticket-po-waiting-chart.js'); ?>
<?= includePageScript('widgets', 'ticket-po-waiting-builder.js'); ?>
