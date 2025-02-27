<?php

namespace Traits\EP;

use Models\EP\EmployeeLinkData;

trait EmployeeLinkTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEmployeeLinkData(array $filters = [], string $limit = ''): array
    {
        $results = (new EmployeeLinkData())->selectEmployeeLinkData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEmployeeLinkItem($result);
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
    private function buildEmployeeLinkItem($result): array
    {
        $item = [
            // Employee
            'employee' => [
                'id' => (int)$result->EMPLOYEE_SEQNO,
                'num' => (int)$result->EMPLOYEE_NUM,
                'name' => (string)htmlentities($result->EMPLOYEE_NAME),
            ],
        ];
        return $item;
    }
}
