<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\EstimatorProfitMarginTrait;

class EstimatorProfitMarginController extends Controller
{
    use EstimatorProfitMarginTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'company_id'   => $data['params']['company_id'] ?? null,
                'project_site'     => $data['params']['project_site'] ?? null
            ];

            // Get Estimator Profit Margin
            $results = $this->getEstimatorProfitMarginData($data['filters']);

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
}
