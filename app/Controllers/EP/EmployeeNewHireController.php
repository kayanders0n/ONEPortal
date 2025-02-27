<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Model;
use Core\View;
use Models\EP\EmployeeNewHireData;

class EmployeeNewHireController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->registry(false);

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'employee/new-hire',
            'title'       => 'Employee New Hire',
            'description' => 'Employee New Hire - Generate Paperwork',
            'author'      => '',
        ];

        View::renderTemplate('ep/employee/new-hire/index', $data);
    }

    public function submit()
    {
        $data = $this->registry(false);

        try {
            $json_data = (!empty($_POST) ? json_encode($_POST) : '');
            $results   = (new EmployeeNewHireData())->submitNewHireData($json_data);

            if ($results['result'] == true) {
                // success so redirect them in 30 seconds to give them time to read the message
                header("refresh:30;url=/employee/new-hire");
            }
        } catch (\Exception $e) {
            $data['message'] = 'Unable to submit data!';
        }

        $data['results'] = $results;

        // Page Object
        $data['page'] = [
            'slug'        => 'employee/new-hire',
            'title'       => 'Employee New Hire - Submit',
            'description' => 'Employee New Hire - Generate Paperwork',
            'author'      => '',
            'action'      => 'Submitted'
        ];

        View::renderTemplate('ep/employee/new-hire/submit', $data);
    }
}
