<?php

namespace Traits\EP;

use Models\EP\AccountingAROpenBillingData;

trait AccountingAROpenBillingTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getAccountingAROpenBillingData(array $filters = [], string $limit = ''): array
    {
        $results = (new AccountingAROpenBillingData())->selectAccountingAROpenBillingData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildAccountingAROpenBillingItem($result);
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
    private function buildAccountingAROpenBillingItem($result): array
    {
        $item = [
            'item' => [
                'item_id' => (int) ($result->TASK_SEQNO ?? null),
            ],
            'type' => [
                'code' => trim($result->TASK_CAPACITYCODE),
                'name' => trim($result->TASK_NAME),
                'total' => (int) $result->TOTAL_COUNT,
            ]

        ];
        return $item;
    }
}
