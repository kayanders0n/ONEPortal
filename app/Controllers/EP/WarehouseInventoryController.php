<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;

class WarehouseInventoryController extends Controller
{

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
            'slug'        => 'warehouse/inventory',
            'title'       => 'Inventory',
            'description' => 'Inventory',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO']        = '/warehouse/po/find';
        $data['page_nav']['WO']        = '/warehouse/wo/find';
        $data['page_nav']['Inventory'] = '/warehouse/inventory';

        View::renderTemplate('ep/warehouse/inventory/index', $data);
    }


}
