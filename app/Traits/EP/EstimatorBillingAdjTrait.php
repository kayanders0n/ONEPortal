<?php

namespace Traits\EP;

use Models\EP\EstimatorBillingAdjData;

trait EstimatorBillingAdjTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEstimatorBillingAdjData(array $filters = [], string $limit = ''): array
    {
        $results = (new EstimatorBillingAdjData())->selectEstimatorBillingAdjData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEstimatorBillingAdjItem($result);
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
    private function buildEstimatorBillingAdjItem($result): array
    {
        $item = [
            'item' => [
                'company_name' => htmlentities(trim($result->COMPANY_NAME)),
                'employee_name' => htmlentities(trim($result->EMPLOYEE_NAME)),
                'item_count' => (int)$result->ITEM_COUNT,
            ]

        ];
        return $item;
    }
}
