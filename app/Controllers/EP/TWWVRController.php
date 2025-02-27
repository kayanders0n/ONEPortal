<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\TWWVRData;
use Traits\EP\TWWVRTrait;

class TWWVRController extends Controller
{
    use TWWVRTrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'twwvr',
            'title'       => 'Visual Record',
            'description' => 'Process Video Record Items',
            'author'      => '',
        ];

        $data['page']['admin_user']    = false;
        $data['page']['admin_twwvr']   = false;
        $data['page']['manager_twwvr'] = false;

        if ($this->hasSecurityToken($this->user_id, 'admin', 'employee', $this->user['user_role'])) {
            $data['page']['admin_user'] = true;
        }

        if ($this->hasSecurityToken($this->user_id, 'admin_twwvr', 'employee', $this->user['user_role'])) {
            $data['page']['admin_twwvr'] = true;
        }

        if ($this->hasSecurityToken($this->user_id, 'manager_twwvr', 'employee', $this->user['user_role'])) {
            $data['page']['manager_twwvr'] = true;
        }

        $data['page']['default_company_id'] = (int) $this->getUserDefaultCompany($this->user_id,'employee', $this->user['user_role']);

        View::renderTemplate('ep/twwvr/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'     => $data['params']['item_id'] ?? null,
                'employee_id' => $data['params']['employee_id'] ?? null,
                'company_id'  => $data['params']['company_id'] ?? null,
                'site'        => $data['params']['site'] ?? null,
                'processed'   => $data['params']['processed'] ?? null,
                'deleted'     => $data['params']['deleted'] ?? null,
                'date'        => $data['params']['date'] ?? null,
                'not_linked'  => $data['params']['not_linked'] ?? null,
                'last_24h'    => $data['params']['last_24h'] ?? null,
                'field_only'  => $data['params']['field_only'] ?? null,
                'super_only'  => $data['params']['super_only'] ?? null,
                'count_only'  => $data['params']['count_only'] ?? null
            ];

            $scope = (string) ($data['params']['scope'] ?? '');

            // Get TWWVR Data
            $results = $this->getTWWVRData($data['filters'], $data['limit'], $scope);

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

            $data = $this->registry();

            if (empty($item_id)) {
                Response::addStatus(400);
                $data = ['message' => Response::$status[400]];
            } else {

                $scope = (string) ($data['params']['scope'] ?? '');
                // Get TWWVR Data
                $result = $this->getTWWVRItem(['item_id' => $item_id], $scope);

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

                // Update TWWVR Data
                $result = (new TWWVRData())->updateTWWVRData($request);

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

