<?php

namespace Traits\EP;

use Models\EP\TWWVRProductivityData;

trait TWWVRProductivityTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getTWWVRProductivityData(array $filters = [], string $limit = ''): array
    {
        $results = (new TWWVRProductivityData())->selectTWWVRProductivityData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTWWVRProductivityItem($result);
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
     * @param string $limit
     *
     * @return array
     */
    private function getTWWVRProductivityTicketData(array $filters = [], string $limit = ''): array
    {
        $results = (new TWWVRProductivityData())->selectTWWVRProductivityTicketData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTWWVRProductivityData($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            return $data['results'][0];
        }

        return $data;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildTWWVRProductivityItem($result): array
    {
        $item = [
            // Uploads
            'upload' => [
                'megabytes' => (int)$result->TOTAL_MEG,
                'total' => (int)$result->TOTAL_COUNT,
            ],
            'employee' => [
                'num' => (int) $result->EMPLOYEE_TABLEID,
                'name' => trim($result->EMPLOYEE_NAME),
                'site' => (int)$result->EMPLOYEE_LOCATION,
            ],
        ];
        return $item;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildTWWVRProductivityData($result): array
    {
        $item = [
            'tickets' => [
                'data_type'   => trim($result->DATA_TYPE),
                'total_count' => (int) $result->TOTAL_COUNT,
            ]
        ];
        return $item;
    }
}
