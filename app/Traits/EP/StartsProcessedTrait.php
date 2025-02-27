<?php

namespace Traits\EP;

use Core\Path;
use Models\EP\StartsProcessedData;

trait StartsProcessedTrait
{
    /**
     * @param array $filters
     *
     * @return array
     */
    private function getStartsProcessedData(array $filters = []): array
    {
        $results = (new StartsProcessedData())->selectStartsProcessedData($filters);

        $show_all = ($this->hasSecurityToken($this->user_id, 'management'));

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildStartsProcessedItem($result, $show_all);
        }

        $data['num_results'] = $results[1];

        return $data;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildStartsProcessedItem($result, $show_all): array
    {
        $employeeid = (int)$result->EMPLOYEE_SEQNO ?? 0;
        $employee_num = (int)$result->EMPLOYEE_NUM ?? 0;
        $employee_name = htmlentities((string)$result->EMPLOYEE_NAME ?? 'Unknown');
        $employee_color = '#' . str_pad(dechex((int)$result->EMPLOYEE_COLOR), 6, '0', STR_PAD_LEFT);

        // employee not active
        if ((int)$result->EMPLOYEE_COMPLETED == 1) {
            $employeeid = -999;
            $employee_name = 'Other';
            $employee_color = '#E4E3E3'; // light grey
        } else if (!$show_all) {
            // not my employee info
            if ($employee_num != $this->user_id) {
                $employeeid = -999;
                $employee_name = 'Other';
                $employee_color = '#E4E3E3'; // light grey
            }
        }

        $item = [
            'item' => [
                'employee_id'      => $employeeid,
                'employee_name'    => $employee_name,
                'employee_color'   => $employee_color,
                'rpt_month'        => (string)$result->RPT_MONTH,
                'starts_processed' => (int)$result->STARTS_PROCESSED,
                'error_count'      => (int)$result->ERROR_COUNT,
            ]
        ];
        return $item;
    }
}
