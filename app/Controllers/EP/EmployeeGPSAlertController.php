<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Models\EP\EmployeeGPSAlertData;
use Traits\EP\EmployeeGPSAlertTrait;

class EmployeeGPSAlertController extends Controller
{
    use EmployeeGPSAlertTrait;

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'employee/gps-alerts',
            'title'       => 'Employee GPS Alerts',
            'description' => 'Show Employee GPS Alerts',
            'author'      => '',
        ];

        $data['page']['has_token'] = false;
        if ($this->hasAnySecurityToken($this->user_id, ['admin', 'gps'])) {
            $data['page']['has_token'] = true;
        }

        View::renderTemplate('ep/employee/gps-alerts/index', $data);
    }

    public function list()
    {
        $data = $this->registry();

        if (Request::isAjax()) {

            $data['filters'] = [
                'item_id'            => $data['params']['item_id'] ?? null,
                'employee_num'       => $data['params']['employee_num'] ?? null,
                'alert_type'         => $data['params']['alert_type'] ?? null,
                'company_id'         => $data['params']['company_id'] ?? null,
                'date_start'         => $data['params']['date_start'] ?? null,
                'date_end'           => $data['params']['date_end'] ?? null,
                'company_site'       => $data['params']['company_site'] ?? null,
                'company_department' => $data['params']['company_department'] ?? null,
                'count_only'         => $data['params']['count_only'] ?? null
            ];

            // Get Hyphen Data
            $results = $this->getEmployeeGPSAlertData($data['filters'], $data['limit']);

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

                // Get GPS Employee Alert Data
                $result = $this->getEmployeeGPSAlertItem(['item_id' => $item_id]);

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
}
