<?php

// App Constants
const BASE_PATH = __DIR__;
const APP_PATH  = BASE_PATH . '/app';
const APP_STATE = 'live'; // App State ('live', 'maintenance')
const APP_ENV   = 'production'; // App Env ('production', 'development')

// Errors
switch (APP_ENV) {
    case 'development':
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        break;
    case 'production':
        ini_set('display_errors', 0);
        break;
    default:
        break;
}

// App Version
require_once BASE_PATH . '/version.php';

if (defined('APP_STATE')) {

    if (APP_STATE === 'maintenance') {

        // Maintenance Access
        $maintenance_access = [
            '127.0.0.1',
            '10.0.0.89',
            '10.0.0.234'
        ];

        if (in_array($_SERVER['REMOTE_ADDR'], $maintenance_access, true)) {

            // Initialize App
            require_once APP_PATH . '/init.php';

        } else {

            // Maintenance Page
//            include BASE_PATH . '/maintenance.php';
        }

    } else if (APP_STATE === 'live') {

        // Initialize App
        require_once APP_PATH . '/init.php';
    }
}
