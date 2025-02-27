<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\WorkOrderTrait;
use Traits\EP\WorkOrderItemTrait;

class WarehouseWOController extends Controller
{

    use WorkOrderTrait;
    use WorkOrderItemTrait;

    public function find()
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'warehouse/wo/find',
            'title'       => 'Find Purchase Order',
            'description' => 'Find a PO',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO'] = '/warehouse/po/find';
        $data['page_nav']['WO'] = '/warehouse/wo/find';
        $data['page_nav']['Inventory'] = '/warehouse/inventory';

        View::renderTemplate('ep/warehouse/wo/find/index', $data);
    }

    public function lookup()
    {
        $data = $this->registry();

        try {
            // Do the thing
            $request = Request::filterRequest($_POST, true);

            $wo_num = 0;
            if ($wo_num == 0) {
                if (isset($request['wo_num'])) {
                    $wo_num = (int) $request['wo_num'];
                }
            }

            $header = 'Location: /warehouse/wo/find?error=Work Order Not Found!';
            if ($wo_num != 0) {
                if ($this->validateWorkOrderNum($wo_num)) {
                    $header = 'Location: /warehouse/wo/' . $wo_num;
                }
            }
            View::addHeader($header);
            View::sendHeaders(); // redirect to page
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }
    }

    public function index($wo_num)
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'warehouse/wo',
            'title'       => 'Work Orders',
            'description' => 'Work Orders',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO'] = '/warehouse/po/find';
        $data['page_nav']['WO'] = '/warehouse/wo/find';


        if ($wo_num != 0) {
            $wo_id = $this->validateWorkOrderNum($wo_num);
            if ($wo_id > 0) {
                $data['page']['wo_id']  = $wo_id;
                $data['page']['wo_num'] = $wo_num;
            } else {
                $header = 'Location: /warehouse/wo?error=Work Order Not Found!';
                View::addHeader($header);
                View::sendHeaders(); // redirect to page
                exit;
            }
        }

        View::renderTemplate('ep/warehouse/wo/index', $data);
    }


}
