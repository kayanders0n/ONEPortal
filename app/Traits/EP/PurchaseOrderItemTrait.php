<?php

namespace Traits\EP;

use Helpers\Numeric;
use Models\EP\PurchaseOrderItemData;
use Helpers\Date;

trait PurchaseOrderItemTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getPurchaseOrderItemData(array $filters = [], string $limit = ''): array
    {
        $results = (new PurchaseOrderItemData())->selectPurchaseOrderItemData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildPurchaseOrderLineItem($result);
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
    private function getPurchaseOrderLineItem(array $filters): ?array
    {
        return $this->getPurchaseOrderItemData($filters, 'ROWS 1');
    }

        /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildPurchaseOrderLineItem($result): array
    {

        $item = [
            // PO item
            'po_item'   => [
                'item_id'        => (int) $result->POITEM_SEQNO,
                'units_ordered'  => Numeric::formatFloat((float) $result->ORDERUNITS, 2, false),
                'units_received' => Numeric::formatFloat((float) $result->RECEIVEDUNITS, 2, false),
                'units_invoiced' => Numeric::formatFloat((float) $result->INVOICEDUNITS, 2, false),
                'add_descript'   => htmlentities(cleanString($result->PO_ADDDESCRIPT)),
                'location'       => htmlentities($result->PO_LOCATION),
                'reference'      => htmlentities($result->PO_REFERENCE),
                'created_on'     => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'created_by'     => $result->CREATEBYNAME,
                'modified_on'    => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'modified_by'    => $result->MODIFIEDBYNAME,

                // Material
                'material' => [
                    'id'         => (int) $result->MATERIAL_SEQNO,
                    'code'       => htmlentities(trim(strtoupper($result->MATERIAL_IDCODE))),
                    'name'       => htmlentities(cleanString($result->MATERIAL_NAME)),
                    'uom'        => htmlentities(cleanString($result->MATERIAL_UOM)),
                ],

                // Job
                'job' => [
                    'id'        => (int) $result->JOB_SEQNO,
                    'num'       => (int)($result->JOB_NUM),
                    'code'      => htmlentities(trim(strtoupper($result->JOB_IDCODE))),
                    'site' => [
                        'id'         => (int) $result->JOBSITE_SEQNO,
                        'num'         => (int) $result->JOBSITE_NUM,
                        'code'       => htmlentities(trim(strtoupper($result->JOBSITE_IDCODE))),
                        'name'       => htmlentities(cleanString($result->JOBSITE_NAME)),
                        'address1'   => htmlentities(cleanString($result->JOBSITE_ADDRESS1)),
                    ],
                    'community' => [
                        'id'    => (int) $result->PROJECT_SEQNO,
                        'num'   => (int) $result->PROJECT_NUM,
                        'name'  => htmlentities($result->PROJECT_NAME),
                    ]
                ],
            ],
        ];

        return $item;
    }

}
