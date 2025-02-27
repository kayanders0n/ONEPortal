<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class DashboardController extends Controller
{
    public function index()
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        $data['page'] = [
            'slug'        => 'dashboard',
            'title'       => 'Dashboard',
            'description' => 'This is a description',
            'author'      => ''
        ];


        if (!empty($this->user['user_name']) && strtoupper($this->user['user_name']) == 'KUSER') {
            $data['page']['meta-refresh'] = 900; // 15 minutes
        }


        if (!empty($this->user['user_name']) && strtoupper($this->user['user_name']) == 'KUSER') {
            if (isset($data['params']['kiosk'])) {
                $data['user']['dashboards'] = ['kiosk_' . $data['params']['kiosk']];

                if (isset($data['params']['default_company_id'])) {
                    $_SESSION['default_company_id'] = (int) $data['params']['default_company_id'];
                }
            }
        }

        $data['page']['default_company_id'] = (int) $this->getUserDefaultCompany($this->user_id, 'employee', $this->user['user_role']);

        View::renderTemplate('ep/index', $data);
    }

    public function dashboard()
    {
        $data = $this->registry();

        $data['page']['default_company_id'] = (int) $this->getUserDefaultCompany($this->user_id, 'employee', $this->user['user_role']);

        View::render('ep/dashboards/' . $data['params']['dash'], $data);
    }
}
