<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\DBConnectionTrait;

class DBConnectionController extends Controller
{
    use DBConnectionTrait;

    public function list()
    {
        $data = $this->registry();

        //if (Request::isAjax()) {

        $data['filters'] = [
            'item_id'      => $data['params']['item_id'] ?? null,
            'connected_on' => $data['params']['connected_on'] ?? null,
            'count_only'   => $data['params']['count_only'] ?? null
        ];

        // Get Database Connections
        $results = $this->getDBConnectionData($data['filters'], $data['limit']);

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

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Hyphen Data
                $result = $this->getDBConnectionItem(['item_id' => $item_id]);

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
