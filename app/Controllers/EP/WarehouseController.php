<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;

class WarehouseController extends Controller
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
            'slug'        => 'warehouse',
            'title'       => 'Warehouse',
            'description' => 'Warehouse',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO']        = '/warehouse/po/find';
        $data['page_nav']['WO']        = '/warehouse/wo/find';
        $data['page_nav']['Inventory'] = '/warehouse/inventory';


        View::renderTemplate('ep/warehouse/index', $data);
    }
}
