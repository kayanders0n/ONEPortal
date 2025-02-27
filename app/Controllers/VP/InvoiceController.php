<?php

namespace Controllers\VP;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Helpers\Url;
use SimpleXMLElement;
use Models\VP\InvoiceData;

class InvoiceController extends Controller
{

    public function postXML()
    {

        if (strpos($_SERVER['CONTENT_TYPE'], 'text/xml') !== false) {
            //if ($_SERVER['CONTENT_TYPE'] == 'text/xml') {

            $params = Url::parseQueryString();

            $request_data = file_get_contents("php://input");
            $xml          = new SimpleXMLElement($request_data);

            $result = (new InvoiceData())->insertInvoiceData($params, $xml);
            if (!empty($result)) {

                Response::addStatus(200);
                Response::sendHeaders();
                $data = [
                    'message' => Response::$status[200],
                    'status'  => 'GOOD',

                ];
            } else {

                Response::addStatus(200);
                Response::sendHeaders();
                $data = [
                    'message' => 'Failed to add to database',
                    'status'  => 'FAILED',

                ];

                error_log('TWW VP Invoice: Unable to add to database');
                error_log($xml->asXML());
            }

        } else {

            Response::addStatus(200);
            Response::sendHeaders();
            $data = [
                'message' => 'Invalid Content Type, use text/xml ' . ' you sent ' . $_SERVER['CONTENT_TYPE'],
                'status'  => 'FAILED',

            ];

            error_log('TWW VP Invoice: Invalid Content Type, use text/xml ' . ' you sent ' . $_SERVER['CONTENT_TYPE']);

        }

        Response::sendHeaders();
        Response::json($data);

    }
}
