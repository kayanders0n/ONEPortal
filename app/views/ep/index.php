<?php
if (!empty($data['user']['dashboards'])) {

    $default_dashboard = $data['params']['dash'] ?? '';

    if (!$default_dashboard) { $default_dashboard = $data['user']['default_dashboard']; }

    echo '<div class="nav-tabs-custom">' . PHP_EOL;
    echo '<ul class="nav nav-tabs">' . PHP_EOL;

    foreach ($data['user']['dashboards'] as $dashboard) {
        $active_tab = ($default_dashboard == $dashboard) ? ' class="active" ' : '';
        echo '<li' . $active_tab . '><a href="#tab-' . $dashboard . '" data-toggle="tab" onclick="LoadDashboard('. "'".$dashboard . "'".');">' . ucwords(str_replace('_', ' ', $dashboard)) . '</a></li>' . PHP_EOL;
    }

    echo '</ul>' . PHP_EOL;
    echo '<div class="tab-content">' . PHP_EOL;

    $first_dash = true;
    foreach ($data['user']['dashboards'] as $dashboard) {
       $show_active = ($default_dashboard == $dashboard) ? ' active': '';
       echo '<div class="tab-pane' . $show_active . '" id="tab-' . $dashboard . '" style="padding: 20px 10px 10px 10px; overflow: hidden">' . PHP_EOL;

        $dash_file = __DIR__ . '/dashboards/' . $dashboard . '.php';
        if (file_exists($dash_file)) {
          if ($default_dashboard) {
            if ($default_dashboard == $dashboard) {
                include_once $dash_file;
            }
          } else {
              if ($first_dash) {
                $first_dash = false;
                include_once $dash_file;
              }
          }

        } else {
            echo 'Undefined dashboard: <span style="color: red;">' . $dashboard . '.</span> Contact your website administrator.';
        }

        echo '</div>' . PHP_EOL;
    }

    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;

}
?>
<script>
  $(function(){
      <?php if ($default_dashboard) : ?>
      $('.nav-tabs a #tab-<?=$default_dashboard?>').tab('show');
      <?php else: ?>
      $('.nav-tabs a:first').tab('show');
      <?php endif; ?>
  });
</script>
<?=includePageScript('dashboards', 'load-dashboard.js');?>

