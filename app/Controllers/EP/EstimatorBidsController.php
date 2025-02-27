<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\EstimatorBidsData;
use Traits\EP\EstimatorBidsTrait;

class EstimatorBidsController extends Controller
{
    use EstimatorBidsTrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'estimator/bids',
            'title'       => 'Estimator Bids',
            'description' => 'Estimator Bids',
            'author'      => '',
        ];

        $data['page_nav']['Report Card'] = '/estimator/reportcard';
        $data['page_nav']['Bids']        = '/estimator/bids';

        $data['page']['default_company_id'] = (int) $this->getUserDefaultCompany($this->user_id,  'employee', $this->user['user_role']);

        View::renderTemplate('ep/estimator/bids/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'     => $data['params']['item_id'] ?? null,
                'date_type'   => $data['params']['date_type'] ?? null,
                'date_start'  => $data['params']['date_start'] ?? null,
                'date_end'    => $data['params']['date_end'] ?? null,
                'search_type' => $data['params']['search_type'] ?? null,
            ];

            // Get Estimator Report Card
            $results = $this->getEstimatorBidsData($data['filters'], $data['limit']);

            // Check Results
            if (empty($results['num_results'])) {
                Response::addStatus(200);
                $data = ['message' => 'No results found'];
            } else {

                Response::addStatus(200);
                $data = [
                    'message'     => Response::$status[200],
                    'results'     => $results['results'],
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

                // Get Estimator Report Card Data
                $result = $this->getEstimatorBidsItem(['item_id' => $item_id]);

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

                // Update Estimator Report Card Data
                $result = (new EstimatorBidsData())->updateEstimatorBidsData($request);

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


    public function add()
    {
        if (Request::isAjax()) {
            $request = Request::filterRequest($_POST, true);

            // Update Estimator Report Card Data
            $result = (new EstimatorBidsData())->updateEstimatorBidsData($request);

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

            Response::sendHeaders();
            Response::json($data);
        }
    }
}



