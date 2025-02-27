<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class EmployeeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'employee',
            'title'       => 'Employee',
            'description' => 'Employee Management',
            'author'      => '',
        ];

        // Page Nav
        if ($this->hasAnySecurityToken($this->user_id, ['admin', 'management', 'payroll_admin', 'super', 'manager'])) {
            $data['page_nav']['Time Clock'] = '/employee/timeclock';
        }
        if ($this->hasAnySecurityToken($this->user_id, ['management', 'super'])) {
            $data['page_nav']['Write-Up'] = '/employee/writeup';
        }
        if ($this->hasAnySecurityToken($this->user_id, ['management', 'gps'])) {
            $data['page_nav']['GPS-Alerts'] = '/employee/gps-alerts';
        }

        if ($this->hasAnySecurityToken($this->user_id, ['admin', 'payroll_admin'])) {
            $data['page_nav']['New Hire'] = '/employee/new-hire';
        }


        View::renderTemplate('ep/employee/index', $data);
    }
}
