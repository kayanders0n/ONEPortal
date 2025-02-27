<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\HyphenData;
use SoapClient;
use Traits\EP\HyphenTrait;

class HyphenController extends Controller
{
    use HyphenTrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'hyphen',
            'title'       => 'Hyphen Order Processing',
            'description' => 'Process Hyphen Orders',
            'author'      => '',
        ];

        $data['page']['has_token'] = false;
        if ($this->hasAnySecurityToken($this->user_id, ['admin', 'hyphen_admin'])) {
            $data['page']['has_token'] = true;
        }

        View::renderTemplate('ep/hyphen/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'    => $data['params']['item_id'] ?? null,
                'type_id'    => $data['params']['type_id'] ?? null,
                'processed'  => $data['params']['processed'] ?? null,
                'deleted'    => $data['params']['deleted'] ?? null,
                'date'       => $data['params']['date'] ?? null,
                'count_only' => $data['params']['count_only'] ?? null
            ];

            // Get Hyphen Data
            $results = $this->getHyphenData($data['filters'], $data['limit']);

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

    public function show(int $item_id)
    {
        if (Request::isAjax()) {

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                // Get Hyphen Data
                $result = $this->getHyphenItem(['item_id' => $item_id]);

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

                // Update Hyphen Data
                $result = (new HyphenData())->updateHyphenData($request);

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

    public function reprocess()
    {
        if (Request::isAjax()) {

            $wdsl = 'http://spconnect.whittoncompanies.com/Connector.asmx?WSDL';

            try {

                $client = new SoapClient($wdsl);

                $status_start = 'Start Processing...';

                $client->ReProcessOrders();

                $status_end = 'End Processing...';

                Response::addStatus(200);
                $data = [
                    'message'      => Response::$status[200],
                    'status_start' => $status_start,
                    'status_end'   => $status_end
                ];

            } catch (\SoapFault $e) {

                Response::addStatus(200);
                $data = [
                    'message' => 'Soap Error',
                    'status'  => $e
                ];
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }
}
