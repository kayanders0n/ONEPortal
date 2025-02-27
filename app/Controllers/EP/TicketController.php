<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class TicketController extends Controller
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
            'slug'        => 'tickets',
            'title'       => 'Tickets',
            'description' => 'Tickets',
            'author'      => ''
        ];

        View::renderTemplate('ep/tickets/index', $data);
    }
}
