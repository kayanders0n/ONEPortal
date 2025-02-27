<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class ApApprovalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = $this->registry();

        // Page Object
        $data['page'] = [
            'slug'        => 'ap-approval',
            'title'       => 'Ap Approval',
            'description' => 'Ap Approval',
            'author'      => ''
        ];

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        View::renderTemplate('ep/ap-approval/index', $data);
    }
}
