<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Exception;
use Helpers\Numeric;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\MaterialTrait;
use Models\EP\MaterialData;

class MaterialController extends Controller
{

    use MaterialTrait;

    public function show(int $item_id)
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'     => $item_id,
                'price_data'  => $data['params']['price_data'] ?? null,
            ];

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Material Data
                $result = $this->getMaterialItem($data['filters']);

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
                'item_id'        => $data['params']['item_id'] ?? null,
                'category_id'    => $data['params']['category_id'] ?? null,
                'code'           => $data['params']['code'] ?? null,
                'upc'            => $data['params']['upc'] ?? null,
                'product_search' => $data['params']['product_search'] ?? null,
                'price_data'     => $data['params']['price_data'] ?? null,
                'count_only'     => $data['params']['count_only'] ?? null
            ];

            // Get Data
            $results = $this->getMaterialData($data['filters'], $data['limit']);

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


    public function listCategory()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [];

            // Get Data
            $results = $this->getMaterialCategoryData($data['filters'], $data['limit']);

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

        $this->registry(); // force login, needed to see the users tokens below

        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $request['item_id'] = $item_id;

                if ($this->hasAnySecurityToken($this->user_id, ['admin', 'warehouse'], 'employee', $this->user['user_role'])) {
                    // Update Material Data
                    $result = (new MaterialData())->updateMaterialData($request);
                }

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

    public function validateUPC($upc)
    {

        if (Request::isAjax()) {

            if (Numeric::isUPC($upc)) {
                Response::addStatus(200);
                $data = [
                    'message' => Response::$status[200],
                    'status'  => 'GOOD'
                ];
            } else {
                Response::addStatus(200);
                $data = ['status' => 'BAD', 'message' => 'Not valid UPC'];
            }

            Response::sendHeaders();
            Response::json($data);

        }

    }
}
