<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\SchedulePhaseCompletedTrait;

class SchedulePhaseCompletedController extends Controller
{
    use SchedulePhaseCompletedTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'company_id'   => $data['params']['company_id'] ?? null,
                'company_site' => $data['params']['company_site'] ?? null,
                'days_old'     => $data['params']['days_old'] ?? null
            ];

            // Get Expired Publish Data, these are items that should be published by now but are stuck for some reason
            $results = $this->getSchedulePhaseCompletedData($data['filters'], $data['limit']);

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
