<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\CommunityListData;
use SoapClient;
use Traits\EP\CommunityListTrait;

class CommunityListController extends Controller
{
    use CommunityListTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'builder_id' => $data['params']['builder_id'] ?? null,
                'count_only' => $data['params']['count_only'] ?? null
            ];

            // Get Data
            $results = $this->getCommunityListData($data['filters'], $data['limit']);

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
