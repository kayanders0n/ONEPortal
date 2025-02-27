<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\WorkOrderTrait;
use Traits\EP\WorkOrderItemTrait;
use Models\EP\WorkOrderData;

class WorkOrderController extends Controller
{

    use WorkOrderTrait;
    use WorkOrderItemTrait;

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get WO Data
                $result = $this->getWorkOrderItem(['item_id' => $item_id]);

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

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'job_id'     => $data['params']['job_id'] ?? null,
                'job_num'    => $data['params']['job_num'] ?? null,
                'count_only' => $data['params']['count_only'] ?? null
            ];

            // Get Data
            $results = $this->getWorkOrderData($data['filters'], $data['limit']);

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

    public function listItems()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'wo_id'      => $data['params']['wo_id'] ?? null,
                'order_view' => $data['params']['order_view'] ?? null,
                'count_only' => $data['params']['count_only'] ?? null
            ];

            // Get Data
            $results = $this->getWorkOrderItemData($data['filters'], $data['limit']);

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


    public function update(int $item_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Work Order Data
                $result = (new WorkOrderData())->updateWorkOrderData($request);

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

    public function print(int $item_id)
    {

        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Purchase Order Data
                $result = (new WorkOrderData())->printWorkOrderData($request);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Did not print'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'printed' => (bool) $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }
}
