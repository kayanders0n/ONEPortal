<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\EstimatorReportCardData;
use Traits\EP\EstimatorReportCardTrait;

class EstimatorReportCardController extends Controller
{
    use EstimatorReportCardTrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'estimator/reportcard',
            'title'       => 'Estimator Report Card',
            'description' => 'Estimator Report Card',
            'author'      => '',
        ];

        $data['page_nav']['Report Card'] = '/estimator/reportcard';
        $data['page_nav']['Bids']        = '/estimator/bids';

        $data['page']['default_company_id'] = (int) $this->getUserDefaultCompany($this->user_id,  'employee', $this->user['user_role']);

        View::renderTemplate('ep/estimator/reportcard/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'       => $data['params']['item_id'] ?? null,
                'company_id'    => $data['params']['company_id'] ?? null,
                'estimator_id'  => $data['params']['estimator_id'] ?? null,
                'builder_id'    => $data['params']['builder_id'] ?? null
            ];

            // Get Estimator Report Card
            $results = $this->getEstimatorReportCardData($data['filters'], $data['limit']);

            // Check Results
            if (empty($results['num_results'])) {
                Response::addStatus(200);
                $data = ['message' => 'No results found'];
            } else {

                Response::addStatus(200);
                $data = [
                    'message'     => Response::$status[200],
                    'results'     => $results['results'],
                    'builders'     => $results['builders'],
                    'num_results' => $results['num_results']
                ];
            }

            Response::sendHeaders();
            Response::json($data);

        }
    }

    public function show(int $item_id, int $company_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id) || empty($company_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Estimator Report Card Data
                $result = $this->getEstimatorReportCardItem(['item_id' => $item_id, 'company_id' => $company_id]);

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
                $result = (new EstimatorReportCardData())->updateEstimatorReportCardData($request);

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



