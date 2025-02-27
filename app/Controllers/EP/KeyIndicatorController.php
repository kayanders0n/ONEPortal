<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class KeyIndicatorController extends Controller
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
            'slug'        => 'key-indicators',
            'title'       => 'Key Indicators',
            'description' => 'Key Indicators',
            'author'      => ''
        ];

        View::renderTemplate('ep/key-indicators/index', $data);
    }
}
