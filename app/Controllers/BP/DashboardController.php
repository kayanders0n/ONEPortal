<?php

namespace Controllers\BP;

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

        View::renderTemplate('bp/index', $data);
    }
}
