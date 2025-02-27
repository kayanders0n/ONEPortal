<?php

namespace Traits\EP;

use Models\EP\EstimatorLotInventoryData;

trait EstimatorLotInventoryTrait
{
    /**
     * @param array $filters
     *
     * @return array
     */
    private function getEstimatorLotInventoryData(array $filters = []): array
    {
        $results = (new EstimatorLotInventoryData())->selectEstimatorLotInventoryData($filters);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEstimatorLotInventoryItem($result);
        }

        $data['num_results'] = $results[1];

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEstimatorLotInventoryItem($result): array
    {
        $item = [
            'item' => [
                'employee_name' => htmlentities(trim($result->EMPLOYEE_NAME)),
                'project_site' => htmlentities(trim($result->PROJ_SITE ?? 'ALL')),
                'lots_remaining_count' => (int)$result->LOTSREMAINING_COUNT,
                'current_jobs_count' => (int)$result->CURRENTJOBS_COUNT,
            ]

        ];
        return $item;
    }
}
