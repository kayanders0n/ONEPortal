<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\NetworkPingTrait;

class NetworkPingController extends Controller
{
    use NetworkPingTrait;

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $networks[] = ['ip'=>'172.16.0.1', 'name'=>'Tucson'];
            $networks[] = ['ip'=>'172.16.20.1', 'name'=>'Buckeye'];
            $networks[] = ['ip'=>'172.16.30.1', 'name'=>'Warner'];

            // Get ping results
            $results = $this->getNetworkPingData($networks);

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
