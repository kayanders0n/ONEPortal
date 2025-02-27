<?php

use Helpers\Session;

$config = [];

$config['env'] = 'production';
if (Session::exists('auth')) {
    $user_auth = unserialize(Session::get('auth'));
    if ($user_auth['user_env'] == 'development') {
        $config['env'] = 'development';
    }
}

// Security JSON File
$config['sec_file'] = APP_PATH . '/config/security.json';

// Temp Directory, make sure that all processes have full permissions directory
$config['temp_path'] = APP_PATH . '/temp/';

// Executable Path
$config['cmd_exe_path'] = 'C:/phpexe/';

// Portal Switch
$http_host = $_SERVER['HTTP_HOST'];
switch ($http_host) {
    // Buyers
    case 'bp.thewhittonway.com':
        $portal_abbr = 'bp';
        $portal_full = 'Builder Portal';
        $user_type   = 'entity';
        break;
    // Vendors
    case 'vp.thewhittonway.com':
        $portal_abbr = 'vp';
        $portal_full = 'Vendor Portal';
        $user_type   = 'entity';
        break;
    // Employees
    default:
        $portal_abbr = 'ep';
        $portal_full = 'Employee Portal';
        $user_type   = 'employee';
        break;
}

// Application
$config['app'] = [
    'name'        => 'The Whitton Way',
    'url'         => $http_host,
    'portal'      => $portal_abbr,
    'portal_full' => $portal_full,
    'user_type'   => $user_type,
    'owner'       => 'Whitton Companies',
    'address'     => '',
    'city'        => '',
    'state'       => '',
    'zip'         => '',
    'phone'       => ''
];

// Meta
$config['meta'] = [
    'title_tag'   => '',
    'description' => '',
    'keywords'    => ''
];

// Email
$config['email'] = [
    'admin'   => '',
    'support' => '',
    'noreply' => ''
];

// Defaults
$config['timezone']    = 'America/Phoenix';
$config['date_format'] = 'Y-m-d H:i:s';
$config['controller']  = 'home';
$config['method']      = 'index';

// Errors
$config['error_default'] = 'An error has occurred.';

// Directories
$config['assets'] = '/assets';
$config['media']  = '/media';
$config['data']   = '/data';
$config['logs']   = [
    'log_group' => 'whitton-tww',
    'log_push'  => 0,
    'log_dir'   => '/logs/' . date('Y') . '/' . date('m') . '/' . date('d') . '/'
];
$config['views']  = '/views';

// Database
$db_host = 'FBSERVER:/db/';
$db_name = 'WHITTON.DB';

if ($config['env'] == 'development') {
    $db_name = 'WHITTON_DEV.DB';
}

$config['db'] = [
    'type'       => 'firebird',
    'host'       => '',
    'name'       => $db_host . $db_name,
    'user'       => 'TWW_WEB',
    'pass'       => 'masterkey',
    'admin_user' => 'SYSDBA',
    'admin_pass' => 'masterkey'
];

$firebird_db_secret = APP_PATH . '/config/secret/firebird.db.secret';
if (is_file($firebird_db_secret)) {
    include $firebird_db_secret;
}

// Document Server HOST  Path
$config['document_server'] = 'SRV001:' . $db_name;

// Services
$config['services'] = [];

$config['app']['version']   = ' TWW ' . APP_VERSION . '-Beta (srv ' . ($_SERVER['COMPUTERNAME'] ?? $_SERVER['SERVER_NAME']) . ', env ' . ucwords($config['env']) . ', db ' . $db_name . ')';
$config['app']['copyright'] = html_entity_decode('&copy;&nbsp;') . date('Y') . ' ' . config('app.owner');

// Local config overrides (git ignores this file)
$local_file = BASE_PATH . '/config.local.php';
if (is_file($local_file)) {
    include $local_file;
}

function config($config_item, $default = null)
{
    global $config;

    return arrayGet($config, $config_item, $default);
}
