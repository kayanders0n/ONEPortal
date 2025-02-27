<?php

use Core\Router;
use Helpers\Request;
use Helpers\Url;

// Auth Redirects
Router::get('login', function () {
    Url::redirect('/auth/login');
});
Router::get('logout', function () {
    Url::redirect('/auth/logout');
});
Router::get('login/legacy', function () {
    Url::redirect('/auth/login/legacy');
});

// Auth Routes
Router::get('auth/login', 'Controllers\Auth\UserAuthController@showLogin');
Router::post('auth/login', 'Controllers\Auth\UserAuthController@doUserLogin');
Router::get('auth/logout', 'Controllers\Auth\UserAuthController@doUserLogout');
Router::get('auth/login/legacy', 'Controllers\Auth\UserAuthController@doLegacyUserLogin');
Router::any('auth/forgot-password', 'Controllers\Auth\UserPasswordController@forgotPassword');
Router::any('auth/validate-passcode', 'Controllers\Auth\UserPasswordController@validatePasscode');
Router::any('auth/reset-password', 'Controllers\Auth\UserPasswordController@resetPassword');

if (config('app.portal') == 'ep') {

    /**
     * Employee Portal Routes
     */

    // Dashboard
    Router::get('', 'Controllers\EP\DashboardController@index');
    Router::get('dashboard', 'Controllers\EP\DashboardController@dashboard');

    // Document
    Router::get('document/download/(:num)', 'Controllers\EP\DocumentController@download');
    Router::get('document/list', 'Controllers\EP\DocumentController@list');

    // Schedule
    Router::get('schedule', 'Controllers\EP\ScheduleController@index');
    Router::get('schedule/phase-completed/list', 'Controllers\EP\SchedulePhaseCompletedController@list');

    // Jobs
    Router::get('jobs', 'Controllers\EP\JobController@find');
    Router::post('jobs/lookup', 'Controllers\EP\JobController@lookup');
    Router::get('jobs/(:num)', 'Controllers\EP\JobController@index');
    Router::get('jobs/show/(:num)', 'Controllers\EP\JobController@show');
    Router::get('jobs/(:num)/tickets', 'Controllers\EP\JobTicketsController@index');
    Router::get('jobs/(:num)/documents', 'Controllers\EP\JobDocumentController@index');
    Router::get('jobs/(:num)/notes', 'Controllers\EP\JobNotesController@index');
    Router::get('jobs/builder/list', 'Controllers\EP\BuilderListController@list');
    Router::get('jobs/community/list', 'Controllers\EP\CommunityListController@list');
    Router::get('jobs/lot/list', 'Controllers\EP\LotListController@list');
    Router::get('jobs/workers/list', 'Controllers\EP\JobWorkersController@list');
    Router::get('jobs/options/list', 'Controllers\EP\JobOptionsController@list');
    Router::get('jobs/labor/list', 'Controllers\EP\JobLaborController@list');
    Router::get('jobs/takeoff/list', 'Controllers\EP\JobTakeoffController@list');
    Router::get('jobs/qa/list', 'Controllers\EP\JobQAController@list');
    Router::post('jobs/update/(:num)', 'Controllers\EP\JobController@update');

    // Tasks
    Router::get('tasks/list', 'Controllers\EP\TasksController@list');
    Router::get('tasks/show/(:num)', 'Controllers\EP\TasksController@show');
    Router::post('tasks/update/(:num)', 'Controllers\EP\TasksController@update');

    // Tickets
    Router::get('tickets', 'Controllers\EP\TicketController@index');
    Router::get('tickets/completed/list', 'Controllers\EP\TicketCompletedController@list');
    Router::get('tickets/po-waiting/list', 'Controllers\EP\TicketPOWaitingController@list');

    // Job Budgets
    Router::get('job-budgets', 'Controllers\EP\JobBudgetController@index');

    // Catalog
    Router::get('catalog', 'Controllers\EP\CatalogController@index');

    // Material
    Router::get('material/upc/(:any)', 'Controllers\EP\MaterialController@validateUPC');
    Router::get('material/list', 'Controllers\EP\MaterialController@list');
    Router::get('material/show/(:num)', 'Controllers\EP\MaterialController@show');
    Router::get('material/category/list', 'Controllers\EP\MaterialController@listCategory');
    Router::post('material/update/(:num)', 'Controllers\EP\MaterialController@update');

    // PO
    Router::get('po/list', 'Controllers\EP\PurchaseOrderController@list');
    Router::get('po/show/(:num)', 'Controllers\EP\PurchaseOrderController@show');
    Router::get('po/items/list', 'Controllers\EP\PurchaseOrderController@listItems');
    Router::get('po/item/show/(:num)', 'Controllers\EP\PurchaseOrderController@showItem');
    Router::post('po/update/(:num)', 'Controllers\EP\PurchaseOrderController@update');
    Router::post('po/print/(:num)', 'Controllers\EP\PurchaseOrderController@print');
    Router::post('po/item/add/(:num)', 'Controllers\EP\PurchaseOrderController@addItem');
    Router::post('po/item/remove/(:num)', 'Controllers\EP\PurchaseOrderController@removeItem');
    Router::post('po/item/update/(:num)', 'Controllers\EP\PurchaseOrderController@updateItem');

    // Work Order
    Router::get('wo/show/(:num)', 'Controllers\EP\WorkOrderController@show');
    Router::get('wo/list', 'Controllers\EP\WorkOrderController@list');
    Router::get('wo/items/list', 'Controllers\EP\WorkOrderController@listItems');
    Router::post('wo/update/(:num)', 'Controllers\EP\WorkOrderController@update');
    Router::post('wo/print/(:num)', 'Controllers\EP\WorkOrderController@print');

    // Warehouse
    Router::get('warehouse', 'Controllers\EP\WarehouseController@index');

    // Warehouse PO
    Router::post('warehouse/po/lookup', 'Controllers\EP\WarehousePOController@lookup');
    Router::get('warehouse/po/find', 'Controllers\EP\WarehousePOController@find');
    Router::get('warehouse/po/(:num)', 'Controllers\EP\WarehousePOController@index');


    // Warehouse WO
    Router::post('warehouse/wo/lookup', 'Controllers\EP\WarehouseWOController@lookup');
    Router::get('warehouse/wo/find', 'Controllers\EP\WarehouseWOController@find');
    Router::get('warehouse/wo/(:num)', 'Controllers\EP\WarehouseWOController@index');

    // Warehouse Inventory
    Router::get('warehouse/inventory', 'Controllers\EP\WarehouseInventoryController@index');


    // Key Indicators
    Router::get('key-indicators', 'Controllers\EP\KeyIndicatorController@index');

    // AP Approval
    Router::get('ap-approval', 'Controllers\EP\APApprovalController@index');

    // Service
    Router::get('service', 'Controllers\EP\ServiceController@index');

    // Hyphen
    Router::get('hyphen', 'Controllers\EP\HyphenController@index');
    Router::get('hyphen/list', 'Controllers\EP\HyphenController@list');
    Router::get('hyphen/show/(:num)', 'Controllers\EP\HyphenController@show');
    Router::post('hyphen/update/(:num)', 'Controllers\EP\HyphenController@update');
    Router::get('hyphen/reprocess', 'Controllers\EP\HyphenController@reprocess');

    // PDOC Files
    Router::get('pdoc-files/list', 'Controllers\EP\PDOCFileController@list');

    // PLS Jobsite
    Router::get('job-starts/list', 'Controllers\EP\PLSJobsiteController@list');

    // Employee
    Router::get('employee', 'Controllers\EP\EmployeeController@index');
    Router::get('employee/gps-alerts', 'Controllers\EP\EmployeeGPSAlertController@index');
    Router::get('employee/gps-alerts/list', 'Controllers\EP\EmployeeGPSAlertController@list');
    Router::get('employee/new-hire', 'Controllers\EP\EmployeeNewHireController@index');
    Router::post('employee/new-hire/submit', 'Controllers\EP\EmployeeNewHireController@submit');
    Router::get('employee/link/list', 'Controllers\EP\EmployeeLinkController@list');
    Router::get('employee/list/list', 'Controllers\EP\EmployeeListController@list');

    // TWWVR
    Router::get('twwvr/productivity/list', 'Controllers\EP\TWWVRProductivityController@list');
    Router::get('twwvr', 'Controllers\EP\TWWVRController@index');
    Router::get('twwvr/list', 'Controllers\EP\TWWVRController@list');
    Router::get('twwvr/show/(:num)', 'Controllers\EP\TWWVRController@show');
    Router::post('twwvr/update/(:num)', 'Controllers\EP\TWWVRController@update');

    // Estimator
    Router::get('estimator', 'Controllers\EP\EstimatorController@index');
    Router::get('estimator/bids', 'Controllers\EP\EstimatorBidsController@index');
    Router::get('estimator/bids/list', 'Controllers\EP\EstimatorBidsController@list');
    Router::get('estimator/bids/show/(:num)', 'Controllers\EP\EstimatorBidsController@show');
    Router::post('estimator/bids/update/(:num)', 'Controllers\EP\EstimatorBidsController@update');
    Router::post('estimator/bids/add', 'Controllers\EP\EstimatorBidsController@add');
    Router::get('estimator/reportcard', 'Controllers\EP\EstimatorReportCardController@index');
    Router::get('estimator/startsproductivity/list', 'Controllers\EP\EstimatorStartsProductivityController@list');
    Router::get('estimator/billingadj/list', 'Controllers\EP\EstimatorBillingAdjController@list');
    Router::get('estimator/errors/list', 'Controllers\EP\EstimatorErrorsController@list');
    Router::get('estimator/startsprocessedlate/list', 'Controllers\EP\EstimatorStartsProcessedLateController@list');
    Router::get('estimator/lotinventory/list', 'Controllers\EP\EstimatorLotInventoryController@list');
    Router::get('estimator/profitmargin/list', 'Controllers\EP\EstimatorProfitMarginController@list');
    Router::get('estimator/reportcard/list', 'Controllers\EP\EstimatorReportCardController@list');
    Router::get('estimator/reportcard/show/(:num)/(:num)', 'Controllers\EP\EstimatorReportCardController@show');
    Router::post('estimator/reportcard/update/(:num)', 'Controllers\EP\EstimatorReportCardController@update');

    // Starts
    Router::get('starts/processed/list', 'Controllers\EP\StartsProcessedController@list');

    // Framing
    Router::get('framing/communityinfo/list', 'Controllers\EP\FramingCommunityInfoController@list');

    // AWS S3 URL
    Router::get('aws-s3/(:any)/url', 'Controllers\EP\AwsS3Controller@url');

    // DB Connection
    Router::get('db/connection/list', 'Controllers\EP\DBConnectionController@list');
    Router::get('db/replication/list', 'Controllers\EP\DBReplicationController@list');

    // Network
    Router::get('network/ping/list', 'Controllers\EP\NetworkPingController@list');

    // Accounting
    Router::get('accounting/ar/open-billing/list', 'Controllers\EP\AccountingAROpenBillingController@list');
    Router::get('accounting/ar/process-queue/list', 'Controllers\EP\ARProcessQueueController@list');

    // Fleet
    Router::get('fleet/list', 'Controllers\EP\FleetController@list');

} else if (config('app.portal') == 'bp') {

    /**
     * Builder Portal Routes
     */

    // Dashboard
    Router::get('', 'Controllers\BP\DashboardController@index');

    // Job QA
    Router::get('job-qa', 'Controllers\BP\JobQAController@index');
    Router::get('job-qa/list', 'Controllers\BP\JobQAController@list');

} else if (config('app.portal') == 'vp') {

    /**
     * Vendor Portal Routes
     */

    // Invoices
    Router::post('invoice/postXML', 'Controllers\VP\InvoiceController@postXML');

}

// Default Route
Router::get('', 'Controllers\HomeController@index');

// Generate Hash
Router::any('hash/(:any)', function (string $param) {
    //$param = 'xxx'; // not supported chars like # will not work on the URL line so hard code it to get it temporarily
    die(password_hash(md5($param), PASSWORD_BCRYPT, ['cost' => 12]));
});

// PHP Info
Router::get('php', function () {
    die(phpinfo());
});

// If no route found
if (Request::isAjax()) {
    Router::error('Controllers\ErrorController@route');
} else {
    Router::error('Controllers\ErrorController@view');
}

// Route fallback to old style
Router::$fallback = false;

// Do the thing
Router::dispatch();
