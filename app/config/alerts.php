<?php

use Helpers\Session;

$alert = [];

$alert_icons = [
    'success' => 'check',
    'info'    => 'info-circle',
    'warning' => 'exclamation-triangle',
    'danger'  => 'ban'
];

// check if the session has alert
$msg = Session::pull('alert');

if (empty($msg)) {
    if (!empty($_GET['alert'])) {
        $msg = $_GET['alert'];
    } else if (!empty($data['alert_type']) && !empty($data['alert_message'])) {
        $msg              = 'custom-alert';
        $alert['type']    = $data['alert_type'];
        $alert['title']   = $data['alert_title'];
        $alert['message'] = $data['alert_message'];
    } else if (!empty($data['alert'])) {
        $msg = $data['alert'];
    } else if (!empty($data['vars']['alert'])) {
        $msg = $data['vars']['alert'];
    }
}

if (!empty($msg)) {

    $alert['timer'] = '';

    switch ($msg) {

        // general message settings
        case 'maintenance':
            $alert['type']    = 'info';
            $alert['title']   = 'NOTICE:';
            $alert['message'] = 'This section is under maintenance. We appreciate your patience and apologize for any inconvenience.';
            break;

        case 'reset-success':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'If the email you entered is valid, an email was sent to that account. Please check your email.';
            break;

        // account settings
        case 'login':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'You have been successfully logged in.';
            $alert['timer']   = 5000;
            break;

        case 'logout':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'You have been successfully logged out.';
            $alert['timer']   = 5000;
            break;

        case 'not-active':
            $alert['type']    = 'warning';
            $alert['title']   = 'Oops, something happened.';
            $alert['message'] = 'There is an issue with your account. Please contact support at ' . config('site_phone') . '.';
            break;

        case 'not-authed':
            $alert['type']    = 'info';
            $alert['title']   = 'NOTICE:';
            $alert['message'] = 'You must be logged in to do that. Please log in below.';
            $alert['timer']   = 5000;
            break;

        case 'no-env':
            $alert['type']    = 'warning';
            $alert['title']   = 'Oops, something happened.:';
            $alert['message'] = 'There is an issue with your account. Please contact support ' . config('site_phone') . '.';
            $alert['timer']   = 5000;
            break;

        case 'invalid-login':
            $alert['type']    = 'warning';
            $alert['title']   = 'Oops, something happened.';
            $alert['message'] = 'Incorrect login credentials. Please try again.';
            $alert['timer']   = 5000;
            break;

        case 'invalid-email':
            $alert['type']    = 'warning';
            $alert['title']   = 'Oops, something happened.';
            $alert['message'] = 'Email address not found. Please check your email and try again.';
            break;

        case 'password-mismatch':
            $alert['type']    = 'warning';
            $alert['title']   = 'Oops, something happened.';
            $alert['message'] = 'Update unsuccessful. The password and confirm password fields must match.';
            $alert['timer']   = 5000;
            break;

        case 'account-updated':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'Your account settings have been successfully updated.';
            $alert['timer']   = 5000;
            break;

        // admin account messages
        case 'user-deactivated':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'You have successfully DEACTIVATED the user';
            $alert['timer']   = 5000;
            break;

        case 'user-activated':
            $alert['type']    = 'success';
            $alert['title']   = 'SUCCESS';
            $alert['message'] = 'You have successfully ACTIVATED the user';
            $alert['timer']   = 5000;
            break;
    }

    if (!empty($alert['type'])) {
        $alert['icon'] = $alert_icons[$alert['type']];
    }
}
