<?php

namespace Traits\EP;

use Models\EP\WorkOrderData;
use Helpers\Date;

trait WorkOrderTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getWorkOrderData(array $filters = [], string $limit = ''): array
    {
        $results = (new WorkOrderData())->selectWorkOrderData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildWorkOrderItem($result);
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
    private function getWorkOrderItem(array $filters): ?array
    {
        return $this->getWorkOrderData($filters, 'ROWS 1');
    }

    /**
     * @param int $wo_num
     * @return int
     */
    private function validateWorkOrderNum(int $wo_num): int
    {
        $result = (new WorkOrderData())->getWorkOrderSeqnoFromNum($wo_num);

        return $result;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildWorkOrderItem($result): array
    {

        $item = [
            // WO
            'wo'   => [
                'item_id'        => (int) $result->WO_SEQNO,
                'num'            => (int) $result->WO_NUM,
                'date'           => Date::formatDate($result->WO_STARTEDON, 'm/d/Y'),
                'status'         => htmlentities($result->STATUS_NAME),
                'status_id'      => (int) $result->WO_STATUSID,
                'name'           => htmlentities($result->WO_NAME),
                'comment'        => htmlentities($result->WO_COMMENT),
                'note'           => nl2br(htmlentities($result->WO_NOTE)),
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
