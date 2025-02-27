<?php

namespace Controllers\EP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\PurchaseOrderTrait;
use Traits\EP\PurchaseOrderItemTrait;

class WarehousePOController extends Controller
{

    use PurchaseOrderTrait;
    use PurchaseOrderItemTrait;

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
            'slug'        => 'warehouse/po/find',
            'title'       => 'Find Purchase Order',
            'description' => 'Find a PO',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO']        = '/warehouse/po/find';
        $data['page_nav']['WO']        = '/warehouse/wo/find';
        $data['page_nav']['Inventory'] = '/warehouse/inventory';

        View::renderTemplate('ep/warehouse/po/find/index', $data);
    }

    public function lookup()
    {
        $data = $this->registry();

        try {
            // Do the thing
            $request = Request::filterRequest($_POST, true);

            $po_num = 0;
            if ($po_num == 0) {
                if (isset($request['po_num'])) {
                    $po_num = (int) $request['po_num'];
                }
            }

            $header = 'Location: /warehouse/po/find?error=PO Not Found!';
            if ($po_num != 0) {
                if ($this->validatePurchaseOrderNum($po_num)) {
                    $header = 'Location: /warehouse/po/' . $po_num;
                }
            }
            View::addHeader($header);
            View::sendHeaders(); // redirect to page
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }
    }

    public function index($po_num)
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (\Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'warehouse/po',
            'title'       => 'Purchase Orders',
            'description' => 'Purchase Orders',
            'author'      => ''
        ];

        // Page Nav to add

        $data['page_nav']['PO'] = '/warehouse/po/find';
        $data['page_nav']['WO'] = '/warehouse/wo/find';


        if ($po_num != 0) {
            $po_id = $this->validatePurchaseOrderNum($po_num);
            if ($po_id > 0) {
                $data['page']['po_id']  = $po_id;
                $data['page']['po_num'] = $po_num;
            } else {
                $header = 'Location: /warehouse/po?error=PO Not Found!';
                View::addHeader($header);
                View::sendHeaders(); // redirect to page
                exit;
            }
        }

        View::renderTemplate('ep/warehouse/po/index', $data);
    }


}
