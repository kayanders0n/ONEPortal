<?php

namespace Traits\EP;

use Models\EP\DBConnectionData;
use Helpers\Date;

trait DBConnectionTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getDBConnectionData(array $filters = [], string $limit = ''): array
    {
        $results = (new DBConnectionData())->selectDBConnectionData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildDBConnectionItem($result);
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
    private function getDBConnectionItem(array $filters): ?array
    {
        return $this->getDBConnectionData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildDBConnectionItem($result): array
    {
        $item = [
            // Connection
            'conn' => [
                'item_id' => (int)$result->ATTACHMENT_ID,
                'ip_address' => $result->REMOTE_ADDRESS,
                'process_name' => basename($result->REMOTE_PROCESS),
                'user_name' => $result->USER_NAME,
                'created_on' => Date::formatDate($result->ATTACHMENT_TIMESTAMP, 'm/d/Y h:i:s A'),
            ],

            // Employee
            'employee' => [
                'id' => (int)$result->EMPLOYEE_SEQNO,
                'num' => $result->EMPLOYEE_TABLEID,
                'name' => $result->EMPLOYEE_NAME,
                'email' => $result->EMPLOYEE_EMAIL,
            ],

        ];
        return $item;
    }
}
