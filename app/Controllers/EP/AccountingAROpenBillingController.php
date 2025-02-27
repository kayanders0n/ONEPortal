<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\AccountingAROpenBillingTrait;

class AccountingAROpenBillingController extends Controller
{
    use AccountingAROpenBillingTrait;

    public function list()
    {
        $data = $this->registry();

        //if (Request::isAjax()) {

        $data['filters'] = [
            'item_id'    => $data['params']['item_id'] ?? null,
            'type_code'  => $data['params']['type_code'] ?? null,
            'by_type'    => $data['params']['by_type'] ?? null,
            'count_only' => $data['params']['count_only'] ?? null
        ];

        // Get Open Billing Tasks
        $results = $this->getAccountingAROpenBillingData($data['filters'], $data['limit']);

        // Check Results
        if (empty($results['num_results'])) {
            Response::addStatus(200);
            $data = ['message' => 'No results found'];
        } else {

            Response::addStatus(200);
            $data = [
                'message'     => Response::$status[200],
                'results'     => $results['results'],
                'num_results' => $results['num_results']
            ];
        }

        Response::sendHeaders();
        Response::json($data);
        //}
    }
}
