<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\EmployeeLinkTrait;

class EmployeeLinkController extends Controller
{
    use EmployeeLinkTrait;

    public function list()
    {
        $data = $this->registry();

        //if (Request::isAjax()) {

        $data['filters'] = [
            'employee_id' => $data['params']['employee_id'] ?? null
        ];

        // Get Database Connections
        $results = $this->getEmployeeLinkData($data['filters'], $data['limit']);

        // Check Results
        if (empty($results['num_results'])) {
            Response::addStatus(200);
            $data = ['message' => 'No results found'];
        } else {

            Response::addStatus(200);
            $data = [
                'message'     => Response::$status[200],
                'results'     => $results['results'] ?? null,
                'num_results' => $results['num_results'] ?? null
            ];
        }

        Response::sendHeaders();
        Response::json($data);
        //}
    }
}
