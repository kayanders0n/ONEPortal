<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\TWWVRProductivityTrait;

class TWWVRProductivityController extends Controller
{
    use TWWVRProductivityTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'company_id'   => $data['params']['company_id'] ?? null,
                'company_site' => $data['params']['company_site'] ?? null,
                'days_old'     => $data['params']['days_old'] ?? null
            ];

            if (isset($data['params']['tickets'])) {
                // Get TWWVR Ticket Productivity
                $results = $this->getTWWVRProductivityTicketData($data['filters'], $data['limit']);
            } else {
                // Get TWWVR Productivity
                $results = $this->getTWWVRProductivityData($data['filters'], $data['limit']);
            }

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
        }
    }


}
