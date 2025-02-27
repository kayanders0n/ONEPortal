<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\PLSJobsiteTrait;

class PLSJobsiteController extends Controller
{
    use PLSJobsiteTrait;

    //TODO create index page for PLS Jobsite
    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'starts',
            'title'       => 'Job Starts',
            'description' => 'Job Starts',
            'author'      => '',
        ];

        $data['page']['has_token'] = false;
        if ($this->hasAnySecurityToken($this->user_id, ['admin'])) {
            $data['page']['has_token'] = true;
        }

        //View::renderTemplate('ep/pls-jobsite/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'         => $data['params']['item_id'] ?? null,
                'expired_publish' => $data['params']['expired_publish'] ?? null,
                'count_only'      => $data['params']['count_only'] ?? null
            ];

            // Get Expired Publish Data, these are items that should be published by now but are stuck for some reason
            $results = $this->getPLSJobsiteData($data['filters'], $data['limit']);

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

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Hyphen Data
                $result = $this->getPLSJobsiteItem(['item_id' => $item_id]);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'No results found'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'result'  => $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

}
