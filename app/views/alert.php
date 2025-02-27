<?php

include APP_PATH . '/config/alerts.php';

if (!empty($alert) && arrayGet($alert, 'type', arrayGet($alert, 'message', ''))) {
    echo '<div class="alert alert-' . $alert['type'] . ' top-alert txt-center' . (!empty($alert['timer']) ? ' alert-timer' : '') . '" role="alert">';
    echo '<i class="fa fa-' . $alert_icons[$alert['type']] . '"></i> &nbsp; <strong>' . $alert['title'] . '</strong> &nbsp;' . $alert['message'];
    if (!empty($alert['timer'])) {
        echo '<script> divAutoClose(".alert-timer", ' . $alert['timer'] . '); </script>';
    }
    echo '</div>';
    echo '<div class="clear"></div>';
}
