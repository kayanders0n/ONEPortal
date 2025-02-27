<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\TicketPOWaitingTrait;

class TicketPOWaitingController extends Controller
{
    use TicketPOWaitingTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'company_id'   => $data['params']['company_id'] ?? null,
                'company_site' => $data['params']['company_site'] ?? null,
                'days_old'     => $data['params']['days_old'] ?? null,
                'by_builder'   => $data['params']['by_builder'] ?? null
            ];

            // Get Waiting PO Tickets
            $results = $this->getTicketPOWaitingData($data['filters'], $data['limit']);

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
