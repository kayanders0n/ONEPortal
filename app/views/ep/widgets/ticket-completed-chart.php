<?php
  if (!isset($loc_default)) { $loc_default = ''; }
  if (!isset($ticket_type_default)) { $ticket_type_default = 'REWORK'; }
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Tickets Completed</h3>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="ticket-completed-form">
        <select id="site" name="site" onchange="LoadTicketCompletedChart();">
          <option value="ALL" selected>All Sites</option>
          <option value="PHX" <?=($loc_default=='PHX')?'selected':'';?>>Phoenix</option>
          <option value="TUC" <?=($loc_default=='TUC')?'selected':'';?>>Tucson</option>
        </select>
        <select id="company" name="company" onchange="LoadTicketCompletedChart();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
        </select>
        <select id="ticket-type" name="ticket-type" onchange="LoadTicketCompletedChart();">
          <option value="PHASE" <?=($ticket_type_default=='PHASE')?'selected':'';?>>Phase</option>
          <option value="REWORK" <?=($ticket_type_default=='REWORK')?'selected':'';?>>Rework</option>
          <option value="WARRANTY" <?=($ticket_type_default=='WARRANTY')?'selected':'';?>>Warranty</option>
          <option value="CONTRACT" <?=($ticket_type_default=='CONTRACT')?'selected':'';?>>Contract</option>
          <option value="PO" <?=($ticket_type_default=='PO')?'selected':'';?>>Builder PO</option>
        </select>
      </form>
    </div>
    <div id="ticket-completed-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body">
    <div class="chart">
      <canvas id="ticket-completed-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
    </div>
  </div>
</div>
<?= includePageScript('widgets', 'ticket-completed-chart.js'); ?>