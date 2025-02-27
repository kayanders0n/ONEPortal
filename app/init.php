<?php

use Core\Logger;
use Helpers\Session;

ob_start();

// Composer Autoload
if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require BASE_PATH . '/vendor/autoload.php';
}

// Session
ini_set('session.gc_maxlifetime', 86400);
define('SESSION_PREFIX', 'tww_');
Session::init();

require APP_PATH . '/config/security.php';
require APP_PATH . '/config/functions.php';
require APP_PATH . '/config/config.php';

// Default Timezone
date_default_timezone_set(config('timezone'));

// Application Constants
define('DEFAULT_CONTROLLER', config('controller'));
define('DEFAULT_METHOD', config('method'));

// Directory Constants
define('ASSETS', BASE_PATH . config('assets'));
define('MEDIA', BASE_PATH . config('media'));
define('DATA', BASE_PATH . config('data'));
define('LOGS', APP_PATH . config('logs.log_dir'));
define('VIEWS', APP_PATH . config('views'));
define('PVIEWS', APP_PATH . config('views') . '/' . config('app.portal'));

// Domain Constants
define('SITE_NAME', config('app.name'));
define('DOMAIN', config('app.url'));
define('HTTP_SERVER', 'http://' . DOMAIN);
define('HTTPS_SERVER', 'https://' . DOMAIN);

// Database Constants
define('DB_TYPE', config('db.type'));
define('DB_HOST', config('db.host'));
define('DB_NAME', config('db.name'));
define('DB_USER', config('db.user'));
define('DB_PASS', config('db.pass'));
define('DB_ADMIN_USER', config('db.admin_user'));
define('DB_ADMIN_PASS', config('db.admin_pass'));

// Custom Definitions
//define('TWW_SITE', 'www'); // DETECT www (Default), vp (Vendor Portal), bp (Builder Portal)

//if (TWW_SITE == 'www') {
//    define('TWW_USER_TYPE', 'employee');
//} else {
//    define('TWW_USER_TYPE', 'entity');
//}

define('MANAGEMENT_ENTITYID', 21443);
define('PLUMBING_ENTITYID', 5633);
define('CONCRETE_ENTITYID', 21440);
define('FRAMING_ENTITYID', 21442);
define('DOORTRIM_ENTITYID', 21444);
define('COOLINGHEATING_ENTITYID', 21445);

define('STATUS_SCHEDULED', 4604036);
define('STATUS_ONHOLD', 21668710);

define('EMPLOYEE_ESTIMATOR_TYPE', 'estimator');
define('EMPLOYEE_BIRTHDAY_TYPE', 'birthday');


// Initialize Logger
(new Logger());

require APP_PATH . '/config/routes.php';
