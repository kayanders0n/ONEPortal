<?php

  $location = Helpers\Location::getLocationByIPAddress();

  if ($location == 'TUCSON') {
    $loc_default = 'TUC';
  } else {
    $loc_default = 'PHX';
  }
?>
<div class="col-md-4">
    <?php
      $ticket_type_default = 'PHASE';
      include PVIEWS . '/widgets/ticket-completed-chart.php';
    ?>
</div>
<div class="col-md-4">
    <?php
      $ticket_type_default = 'REWORK';
      include PVIEWS . '/widgets/ticket-completed-chart2.php';
    ?>
</div>
<div class="col-md-4">
    <?php include PVIEWS . '/widgets/sched-phase-completed-chart.php'; ?>
</div>
<div class="col-md-4">
    <?php include PVIEWS . '/widgets/ticket-po-waiting-chart.php'; ?>
</div>
<div class="col-md-4">
    <?php include PVIEWS . '/widgets/twwvr-tickets-chart.php'; ?>
</div>
<div class="col-md-4">
    <?php include PVIEWS . '/widgets/twwvr-productivity.php'; ?>
</div>
<div style="clear:both;"></div>
<p>As of: <?=date('m/d/Y h:ia');?></p>
