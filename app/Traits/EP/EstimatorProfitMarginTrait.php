<?php

namespace Traits\EP;

use Models\EP\EstimatorProfitMarginData;

trait EstimatorProfitMarginTrait
{
    /**
     * @param array $filters
     *
     * @return array
     */
    private function getEstimatorProfitMarginData(array $filters = []): array
    {
        $results = (new EstimatorProfitMarginData())->selectEstimatorProfitMarginData($filters);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEstimatorProfitMarginItem($result);
        }

        $data['num_results'] = $results[1];

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEstimatorProfitMarginItem($result): array
    {
        $item = [
            'item' => [
                'employee_name' => htmlentities(trim($result->EMPLOYEE_NAME)),
                'project_site' => htmlentities(trim($result->PROJ_SITE ?? 'ALL')),
                'profit_margin' => (float)$result->PROFIT_MARGIN,
            ]
        ];
        return $item;
    }
}
