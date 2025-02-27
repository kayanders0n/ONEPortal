<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\JobLaborData;
use Traits\EP\JobLaborTrait;

class JobLaborController extends Controller
{
    use JobLaborTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'job_id'     => $data['params']['job_id'] ?? null,
                'company_id' => $data['params']['company_id'] ?? null
            ];

            // Get Data
            $results = $this->getJobLaborData($data['filters'], $data['limit']);

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
