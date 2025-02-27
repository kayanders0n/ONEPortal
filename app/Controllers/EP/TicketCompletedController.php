<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\TicketCompletedTrait;

class TicketCompletedController extends Controller
{
    use TicketCompletedTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'company_id'   => $data['params']['company_id'] ?? null,
                'company_site' => $data['params']['company_site'] ?? null,
                'ticket_type'  => $data['params']['ticket_type'] ?? null
            ];

            // Get Completed Tickets
            $results = $this->getTicketCompletedData($data['filters'], $data['limit']);

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
        }
    }
}
