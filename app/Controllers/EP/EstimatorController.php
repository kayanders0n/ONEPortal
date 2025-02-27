<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class EstimatorController extends Controller
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
            'slug'        => 'estimator',
            'title'       => 'Estimator',
            'description' => 'Estimator',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['Report Card'] = '/estimator/reportcard';
        $data['page_nav']['Bids']        = '/estimator/bids';


        View::renderTemplate('ep/estimator/index', $data);
    }
}
