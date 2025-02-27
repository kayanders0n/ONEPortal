<?php

namespace Traits\EP;

use Models\EP\EmployeeGPSAlertData;
use Helpers\Date;

trait EmployeeGPSAlertTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEmployeeGPSAlertData(array $filters = [], string $limit = ''): array
    {
        $results = (new EmployeeGPSAlertData())->selectEmployeeGPSAlertData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEmployeeGPSAlertItem($result);
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
    private function getEmployeeGPSAlertItem(array $filters): ?array
    {
        return $this->getEmployeeGPSAlertData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEmployeeGPSAlertItem($result): array
    {
        $item = [
            // Alert
            'alert'   => [
                'item_id'       => (int) $result->SEQNO,
                'date'          => Date::formatDate($result->ACTIVITYDATE, 'm/d/Y h:i:s A'),
                'date_time'     => Date::formatDate($result->ALERTTIME, 'm/d/Y h:i:s A'),
                'type'          => htmlentities($result->ALERTTYPE),
                'name'          => htmlentities($result->ALERTNAME),
                'condition'     => htmlentities($result->ALERTCONDITION),
                'latitude'      => htmlentities($result->ALERTLATITUDE),
                'longitude'     => htmlentities($result->ALERTLONGITUDE),
                'created_on'    => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
            ],

            // Vehicle
            'vehicle' => [
                'id' => (int)$result->MATERIAL_SEQNO,
                'asset_id' => (int) $result->MATERIALASSETID,
                'num' => (int)$result->MATERIALASSET_IDCODE,
                'name' => htmlentities($result->MATERIAL_NAME),
            ],

            // Employee
            'employee'  => [
                'id'      => (int) $result->EMPLOYEEID,
                'num'     => (int) $result->EMPLOYEE_TABLEID,
                'name'    => htmlentities($result->EMPLOYEE_NAME),
            ],

            // Company
            'company'     => [
                'id'   => (int) $result->COMPANY_SEQNO,
                'num'  => (int) $result->COMPANY_TABLEID,
                'name' => htmlentities($result->COMPANY_NAME),
            ],
        ];

        return $item;
    }
}
