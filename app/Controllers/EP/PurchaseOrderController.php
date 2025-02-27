<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\PurchaseOrderTrait;
use Traits\EP\PurchaseOrderItemTrait;
use Models\EP\PurchaseOrderData;
use Models\EP\PurchaseOrderItemData;

class PurchaseOrderController extends Controller
{

    use PurchaseOrderTrait;
    use PurchaseOrderItemTrait;

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get PO Data
                $result = $this->getPurchaseOrderItem(['item_id' => $item_id]);

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
                'item_id'     => $data['params']['item_id'] ?? null,
                'company_id'  => $data['params']['company_id'] ?? null,
                'type'        => $data['params']['type'] ?? null,
                'date'        => $data['params']['date'] ?? null,
                'reference'   => $data['params']['reference'] ?? null,
                'completed'   => $data['params']['completed'] ?? -1,
            ];

            // Get Data
            $results = $this->getPurchaseOrderData($data['filters'], $data['limit']);

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

    public function showItem(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Data
                $result = $this->getPurchaseOrderLineItem(['item_id' => $item_id]);

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

    public function listItems()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'    => $data['params']['item_id'] ?? null,
                'po_id'      => $data['params']['po_id'] ?? null,
                'count_only' => $data['params']['count_only'] ?? null
            ];

            // Get Data
            $results = $this->getPurchaseOrderItemData($data['filters'], $data['limit']);

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

                // Update Purchase Order Data
                $result = (new PurchaseOrderData())->updatePurchaseOrderData($request);

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

    public function updateItem(int $item_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Purchase Order Data
                $result = (new PurchaseOrderItemData())->updatePurchaseOrderItemData($request);

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

    public function addItem(int $po_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($po_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['po_id'] = $po_id;

                // Update Purchase Order Data
                $result = (new PurchaseOrderItemData())->addPurchaseOrderItemData($request);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Did not add'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'added' => (bool) $result
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

    public function removeItem(int $item_id)
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                // Update Purchase Order Data
                $result = (new PurchaseOrderItemData())->removePurchaseOrderItemData($request);

                // Check Results
                if (empty($result)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Did not remove'];
                } else {

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'removed' => (bool) $result
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
                $result = (new PurchaseOrderData())->printPurchaseOrderData($request);

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
