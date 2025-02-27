<?php

namespace Traits\EP;

use Helpers\Numeric;
use Models\EP\WorkOrderItemData;
use Helpers\Date;

trait WorkOrderItemTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getWorkOrderItemData(array $filters = [], string $limit = ''): array
    {
        $results = (new WorkOrderItemData())->selectWorkOrderItemData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildWorkOrderLineItem($result);
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
    private function getWorkOrderLineItem(array $filters): ?array
    {
        return $this->getWorkOrderItemData($filters, 'ROWS 1');
    }

        /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildWorkOrderLineItem($result): array
    {
        $wh_status_style = '';

        switch ($result->WO_WHSTATUS) {
            case 'RETURNED': $wh_status_style = 'background-color: yellow;'; break;
            case 'SHIPPED': $wh_status_style = 'background-color: olive; color: white;'; break;
            case 'SENT OUT R/M': $wh_status_style = 'background-color: red;'; break;
            case 'LATE CHANGES': $wh_status_style = 'background-color: blue; color: white;'; break;
            case 'LATE EDITIONS': $wh_status_style = 'background-color: purple; color: white;'; break;
            case '100% COMPLETE': $wh_status_style = 'background-color: lightgrey;'; break;
            case 'PO BUILDER': $wh_status_style = 'background-color: orange;'; break;
            case 'DEFECTIVE C/O': $wh_status_style = 'background-color: lime;'; break;
        }

        $wh_status = [
            'name'    => htmlentities($result->WO_WHSTATUS),
            'style'    => $wh_status_style,
        ];

        $item = [
            // PO item
            'wo_item'   => [
                'item_id'        => (int) $result->WOITEM_SEQNO,
                'units'          => Numeric::formatFloat((float) $result->UNITS, 2, false),
                'add_descript'   => htmlentities(cleanString($result->WO_ADDDESCRIPT)),
                'location'       => htmlentities($result->WO_LOCATION),
                'reference'      => htmlentities($result->WO_REFERENCE),
                'wh_status'      => $wh_status,
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
            ],
        ];

        return $item;
    }

}
