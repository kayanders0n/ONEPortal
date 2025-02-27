<?php

namespace Traits\EP;

use Models\EP\DBReplicationData;
use Helpers\Date;

trait DBReplicationTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getDBReplicationData(array $filters = [], string $limit = ''): array
    {
        $results = (new DBReplicationData())->selectDBReplicationData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildDBReplicationItem($result);
        }

        $data['num_results'] = $results[1];


        $results = (new DBReplicationData())->selectDBReplicationErrorData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results_error'][$i] = $this->buildDBReplicationErrorItem($result);
        }

        $data['num_results_error'] = $results[1];

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
    private function buildDBReplicationItem($result): array
    {
        $item = [
            // Replication
            'repl' => [
			    'id' => (int)$result->ID,
                'table_name' => htmlentities($result->TABLE_NAME),
                'created_on' => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
            ],

        ];
        return $item;
    }

    private function buildDBReplicationErrorItem($result): array
    {
        $item = [
            // Error
            'err' => [
                'id' => (int)$result->ID,
                'module_name' => htmlentities($result->PSQL_MODULE),
                'created_on' => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
            ],

        ];
        return $item;
    }
}
