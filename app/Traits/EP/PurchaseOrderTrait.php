<?php

namespace Traits\EP;

use Models\EP\PurchaseOrderData;
use Helpers\Date;

trait PurchaseOrderTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getPurchaseOrderData(array $filters = [], string $limit = ''): array
    {
        $results = (new PurchaseOrderData())->selectPurchaseOrderData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildPurchaseOrderItem($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            if (empty($results[0])) {
                return array(); // no data
            } else {
                return $data['results'][0];
            }
        }

        return $data;
    }

    /**
     * @param array $filters
     *
     * @return array|null
     */
    private function getPurchaseOrderItem(array $filters): ?array
    {
        return $this->getPurchaseOrderData($filters, 'ROWS 1');
    }

    /**
     * @param int $po_num
     * @return int
     */
    private function validatePurchaseOrderNum(int $po_num): int
    {
        $result = (new PurchaseOrderData())->getPurchaseOrderSeqnoFromNum($po_num);

        return $result;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildPurchaseOrderItem($result): array
    {

        $item = [
            // PO
            'po'   => [
                'item_id'        => (int) $result->PO_SEQNO,
                'num'            => (int) $result->PO_NUM,
                'type'           => htmlentities($result->PO_TYPE),
                'type_id'        => (int) $result->PO_DATATYPEID,
                'date'           => Date::formatDate($result->PODATE, 'm/d/Y'),
                'status'         => htmlentities($result->STATUS_NAME),
                'status_id'      => (int) $result->PO_STATUSID,
                'name'           => htmlentities($result->PO_NAME),
                'comment'        => htmlentities($result->PO_COMMENT),
                'note'           => nl2br(htmlentities($result->PO_NOTE)),
                'completed_date' => Date::formatDate($result->PO_COMPLETEDON, 'm/d/Y'),
                'created_on'     => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'created_by'     => $result->CREATEBYNAME,
                'modified_on'    => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'modified_by'    => $result->MODIFIEDBYNAME,
            ],
            // Source Company
            'company' => [
                'id'         => (int) $result->SOURCEENTITY_SEQNO,
                'num'        => (int) $result->SOURCEENTITY_NUM,
                'name'       => htmlentities($result->SOURCEENTITY_NAME),
            ],
            // Vendor
            'vendor' => [
                'id'         => (int) $result->VENDOR_SEQNO,
                'num'        => (int) $result->VENDOR_NUM,
                'name'       => htmlentities($result->VENDOR_NAME),
            ],
            // Ship To
            'shipto' => [
                'id'         => (int) $result->SHIP_SEQNO,
                'num'        => (int) $result->SHIP_NUM,
                'name'       => htmlentities($result->SHIP_NAME),
            ],

            // Job
            'job' => [
                'id'        => (int) $result->JOB_SEQNO,
                'num'       => (int) $result->JOB_NUM,
                'site' => [
                    'id'    => (int) $result->JOBSITE_SEQNO,
                    'num'   => (int) $result->JOBSITE_NUM,
                    'code'  => $result->JOBSITE_IDCODE,
                    'name'  => htmlentities($result->JOBSITE_NAME),
                    'address1' => htmlentities(cleanString($result->JOBSITE_ADDRESS1)),
                    'address2' => htmlentities(cleanString($result->JOBSITE_ADDRESS2)),
                    'city'     => htmlentities(cleanString($result->JOBSITE_CITY)),
                    'state'    => htmlentities(strtoupper($result->JOBSITE_STATE)),
                    'zip'      => htmlentities(cleanString($result->JOBSITE_ZIP)),
                ],
                'community' => [
                    'id'    => (int) $result->PROJECT_SEQNO,
                    'num'   => (int) $result->PROJECT_NUM,
                    'name'  => htmlentities($result->PROJECT_NAME),
                ],
                'builder' => [
                    'id'    => (int) $result->BUILDER_SEQNO,
                    'num'   => (int) $result->BUILDER_NUM,
                    'name'  => htmlentities($result->BUILDER_NAME),
                ],
            ],
        ];

        return $item;
    }

}
