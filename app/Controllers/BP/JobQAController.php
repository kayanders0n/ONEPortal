<?php

namespace Controllers\BP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\BP\JobQATrait;

class JobQAController extends Controller
{
    use JobQATrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'job-qa',
            'title'       => 'Job Quality Assurance',
            'description' => 'Job Quality Assurance'
        ];

        View::renderTemplate('bp/job-qa/index', $data);
    }

    public function list()
    {
        if (Request::isAjax()) {

            $data = $this->registry();

            $data['filters'] = [
                'item_id'    => $data['params']['item_id'] ?? null,
                'job_id'     => $data['params']['type_id'] ?? null,
                'builder_id' => $data['params']['builder_id'] ?? null
            ];

            // Get Job QA
            $results = $this->getJobQA($data['filters'], $data['limit']);

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
