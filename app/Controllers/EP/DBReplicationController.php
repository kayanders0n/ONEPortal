<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\DBReplicationData;
use Traits\EP\DBReplicationTrait;

class DBReplicationController extends Controller
{
    use DBReplicationTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'count_only' => $data['params']['count_only'] ?? null,
            ];

            // Get TWWVR Data
            $results = $this->getDBReplicationData($data['filters'], $data['limit']);

            // Check Results
            //if (empty($results['num_results']) && empty($results['num_results_error'])) {
            //    Response::addStatus(200);
            //    $data = ['message' => 'No results found'];
            //} else {
            // always tell us the counts even if there is not item data
                Response::addStatus(200);
                $data = [
                    'message'     => Response::$status[200],
                    'results'     => $results['results'] ?? null,
                    'num_results' => $results['num_results'] ?? 0,
                    'results_error'     => $results['results_error'] ?? null,
                    'num_results_error' => $results['num_results_error'] ?? 0
                ];
            //}

            Response::sendHeaders();
            Response::json($data);
        }
    }
}

