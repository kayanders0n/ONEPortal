<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Exception;
use Helpers\Request;
use Helpers\Response;
use Models\EP\TasksData;
use Traits\EP\TasksTrait;

class TasksController extends Controller
{
    use TasksTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'          => $data['params']['item_id'] ?? null,
                'data_type_id'     => $data['params']['data_type_id'] ?? null,
                'data_id'          => $data['params']['data_id'] ?? null,
                'type_id'          => $data['params']['type_id'] ?? null,
                'ticket_type_code' => $data['params']['ticket_type_code'] ?? null
            ];

            // Get Tasks
            $results = $this->getTasksData($data['filters'], $data['limit']);

            // Check Results
            if (empty($results['num_results'])) {
                Response::addStatus(200);
                $data = ['message' => 'No results found'];
            } else {

                Response::addStatus(200);
                $data = [
                    'message' => Response::$status[200],
                    'results' => $results['results'],
                    'num_results' => $results['num_results']
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

                // Get Jobs Tasks Data
                $result = $this->getTasksItem(['item_id' => $item_id]);

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

    public function update(int $item_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Tasks Data
                $result = (new TasksData())->updateTasksData($request);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Did not update'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'updated' => (bool) $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

}
