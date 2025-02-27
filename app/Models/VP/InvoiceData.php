<?php

namespace Models\VP;

use Core\Model;
use PDOException;
use Core\Logger;

class InvoiceData extends Model
{
    /**
     * @param $params
     * @param $xml
     * @return int
     */
    public function insertInvoiceData($params, $xml): int
    {

        $is_valid = false;
        $inv_items = [];

        $invnum = '';
        $invamt = 0;
        $invdate = '';
        $ponum = 0;

        $username = strtoupper($params['user']);

        if (!empty($params['user']) && strtoupper($params['user']) == 'CENTRAL') {

            $username = 'CENTRAL_EDI';

            $invnum = $xml->invoice->attributes()['id'];
            $invamt = (float)$xml->invoice->attributes()['total_amt'];
            $invdate = strtotime($xml->invoice->invoiced_on);
            $ponum = (int)$xml->invoice->customer_po;

            $inv_items = [];

            $is_valid = true;
            foreach ($xml->invoice->items->item as $item) {
                $inv_item = ['code' => trim($item->ccode),
                    'units' => (float)$item->shipped,
                    'unit_price' => (float)$item->unit_price
                ];
                if ($inv_item['code'] == '') {
                    $is_valid = false;
                    break;
                } // missing customer code for material item cannot process this invoice
                if ($inv_item['units'] <> 0) { // must have non-zero units
                    array_push($inv_items, $inv_item);
                }
            }
        } else if (!empty($params['user']) && (strtoupper($params['user']) == 'HAJOCA') || (strtoupper($params['user']) == 'HUGHES')) {

            $username = 'HAJHUG_EDI';

            $invnum = $xml->Request->InvoiceDetailRequest->InvoiceDetailRequestHeader->attributes()['invoiceID'];
            $invdate = strtotime($xml->Request->InvoiceDetailRequest->InvoiceDetailRequestHeader->attributes()['invoiceDate']);
            $ponum = (int)$xml->Request->InvoiceDetailRequest->InvoiceDetailOrder->InvoiceDetailOrderInfo->OrderIDInfo->attributes()['orderID'];
            $invamt = (float)$xml->Request->InvoiceDetailRequest->InvoiceDetailSummary->GrossAmount->Money;

            $inv_items = [];

            $is_valid = true;
            foreach ($xml->Request->InvoiceDetailRequest->InvoiceDetailOrder->InvoiceDetailItem as $item) {
                $inv_item = ['code' => trim($item->InvoiceDetailItemReference->ItemID->BuyerPartID),
                             'units' => (float)$item->attributes()['quantity'],
                             'unit_price' => (float)$item->UnitPrice->Money
                ];
                if ($inv_item['code'] == '') {
                    $is_valid = false;
                    break;
                } // missing customer code for material item cannot process this invoice
                if ($inv_item['units'] <> 0) { // must have non-zero units
                    array_push($inv_items, $inv_item);
                }

            }

        } else if (!empty($params['user']) && strtoupper($params['user']) == 'FERGUSON') {

            $username = 'FERGUSON';

            // name space setup
            $ns = $xml->getNamespaces();

            $data = $xml->children($ns['esb'])->Body;
            $data = $data->children();

            $invnum = $data->ProcessInvoice->DataArea->Invoice->InvoiceHeader->DocumentID->ID;
            $invdate = strtotime(str_replace('T00:00:00Z', '', $data->ProcessInvoice->DataArea->Invoice->InvoiceHeader->DocumentDateTime));
            $ponum = (int)$data->ProcessInvoice->DataArea->Invoice->InvoiceHeader->DocumentReference->DocumentID->ID;

            //$invext = (float)$data->ProcessInvoice->DataArea->Invoice->InvoiceHeader->ExtendedAmount;
            $invamt = (float)$data->ProcessInvoice->DataArea->Invoice->InvoiceHeader->TotalAmount;

            $inv_items = [];

            $is_valid = true;

            foreach ($data->ProcessInvoice->DataArea->Invoice->InvoiceLine as $inv_item) {
                $code = trim($inv_item->Item->CustomerItemID->ID);
                //$upc = $inv_item->Item->UPCID; // possibly use for item matching
                $units = (float)$inv_item->Quantity;
                $price = (float)$inv_item->InvoiceCharge->Amount;

                // get the shipped units we should be invoiced for
                foreach ($inv_item->InvoiceSubLine as $sub_line) {
                    foreach ($sub_line->Quantity->attributes() as $key => $value) {
                        if (($key == 'unitCode') && ($value == 'shipped')) {
                            $units = (float)$sub_line->Quantity;
                            break;
                        }
                    }
                }

                $inv_item = [
                    'code' => $code,
                    'units' => $units,
                    'unit_price' => $price
                ];
                if ($inv_item['code'] == '') {
                    $is_valid = false;
                    break;
                } // missing customer code for material item cannot process this invoice
                if ($inv_item['units'] <> 0) { // must have non-zero units
                    array_push($inv_items, $inv_item);
                }
            }

        } else if (!empty($params['user']) && strtoupper($params['user']) == 'CANYON') {

            $username = 'CANYON';

            error_log('CANYON: ' . print_r($xml, true));

        }

        if ($ponum != 0) {

            $result = false;

            $this->db->beginTransaction();

            try {

                $insert = [
                    'VENDORCODE' => strtoupper($params['user']),
                    'VENDORINVOICENUM' => $invnum,
                    'PURCHASEORDERNUM' => $ponum,
                    'INVOICEDATE' => date('m/d/Y', $invdate),
                    'INVOICETOTAL' => $invamt,
                    'XMLDATA' => $xml->asXML(),
                    'CREATEDON' => date('m/d/Y H:i:s'),
                    'CREATEDBYNAME' => $username,
                    'MODIFIEDON' => date('m/d/Y H:i:s'),
                    'MODIFIEDBYNAME' => $username,
                ];

                $value = $this->db->insert('APINVOICE_DATA', $insert, 'SEQNO');

                $id = (int)$value;
                if ($id != 0) {

                    if ($is_valid) {

                        if ($id != 0) {
                            foreach ($inv_items as $item) {
                                $insert = ['DATAID' => $id,
                                    'CODE' => $item['code'],
                                    'UNITS' => $item['units'],
                                    'PRICE' => $item['unit_price'],
                                    'CREATEDON' => date('m/d/Y H:i:s'),
                                    'CREATEDBYNAME' => $username,
                                ];
                                $this->db->insert('APINVOICE_DATA_ITEM', $insert);
                            }
                        }

                        $this->db->exec('EXECUTE PROCEDURE PROCESS_APINVOICE_DATA('. $id . ');');

                    } else { // mark as deleted since we can't process it anywqy

                        $update = ['DELETED' => 1,
                                   'DELETEDON' => date('m/d/Y H:i:s'),
                                   'DELETEDBYNAME' => 'SYSTEM'
                                  ];
                        $where = ['SEQNO' => $id];

                        $this->db->update('APINVOICE_DATA', $update, $where);

                    }
                }
                $this->db->commit();
                $result = true;
            } catch (PDOException $e) {
                $this->db->rollBack();

                //in the event of an error record the error
                //Logger::newMessage($e);

                error_log('InsertInvoice Error: ' . print_r($e, true));
            }

            return $result;
        } else {
            return false;
        }

    }
}
