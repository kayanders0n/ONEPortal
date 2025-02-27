<?php

namespace Traits\EP;

use Helpers\Date;
use Models\EP\EmployeeListData;

trait EmployeeListTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEmployeeListData(array $filters = [], string $limit = ''): array
    {
        $results = (new EmployeeListData())->selectEmployeeListData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEmployeeListItem($result);
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
    private function buildEmployeeListItem($result): array
    {
        $item = [
            'employee' => [
                'item_id'  => (int)$result->EMPLOYEE_SEQNO,
                'num' => (int)$result->EMPLOYEE_NUM,
                'name' => htmlentities(trim($result->EMPLOYEE_NAME)),
                'first_name' => htmlentities(trim($result->EMPLOYEE_FIRSTNAME)),
                'last_name' => htmlentities(trim($result->EMPLOYEE_LASTNAME)),
                'birthdate' => Date::formatDate($result->EMPLOYEE_BIRTHDATE, 'm/d/Y'),
                'birthdate_short' => Date::formatDate($result->EMPLOYEE_BIRTHDATE, 'M jS')
            ]
        ];
        return $item;
    }
}
