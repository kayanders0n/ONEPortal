<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class JobBudgetController extends Controller
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
            'slug'        => 'job-budgets',
            'title'       => 'Job Budgets',
            'description' => 'Job Budgets',
            'author'      => ''
        ];

        View::renderTemplate('ep/job-budgets/index', $data);
    }
}
