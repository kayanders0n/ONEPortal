<?php

namespace Traits\EP;

use Models\EP\ARProcessQueueData;

trait ARProcessQueueTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getARProcessQueueData(array $filters = [], string $limit = ''): array
    {
        $results = (new ARProcessQueueData())->selectARProcessQueueData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildARProcessQueueItem($result);
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
    private function buildARProcessQueueItem($result): array
    {
        $item = [
            'item' => [
                'item_count' => (int)$result->ITEM_COUNT,
                'employee_id' => htmlentities(trim($result->EMPLOYEE_ID)),
                'employee_name' => htmlentities(trim($result->EMPLOYEE_NAME)),
            ]

        ];
        return $item;
    }
}
