<?php

namespace Traits\EP;

use Models\EP\SchedulePhaseCompletedData;

trait SchedulePhaseCompletedTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getSchedulePhaseCompletedData(array $filters = [], string $limit = ''): array
    {
        $results = (new SchedulePhaseCompletedData())->selectSchedulePhaseCompletedData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildSchedulePhaseCompletedItem($result);
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
     * @param $result (std object)
     *
     * @return array
     */
    private function buildSchedulePhaseCompletedItem($result): array
    {
        $item = [
            'item' => [
                'data_type' => trim($result->DATA_TYPE),
                'phase_code' => trim($result->PHASE_CODE),
                'phase_name' => trim($result->PHASE_NAME),
                'total' => (int)$result->TOTAL_COUNT,
            ],

        ];
        return $item;
    }
}
