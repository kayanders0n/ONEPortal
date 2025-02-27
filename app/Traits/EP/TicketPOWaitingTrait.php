<?php

namespace Traits\EP;

use Models\EP\TicketPOWaitingData;

trait TicketPOWaitingTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getTicketPOWaitingData(array $filters = [], string $limit = ''): array
    {
        $results = (new TicketPOWaitingData())->selectTicketPOWaitingData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTicketPOWaitingItem($result);
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
    private function buildTicketPOWaitingItem($result): array
    {
        $item = [
            'item' => [
                'data_type' => trim($result->DATA_TYPE),
                'total_amount' => '$'.number_format((float)$result->TOTAL_COST,2),
                'total_count' => (int)$result->TOTAL_COUNT,
            ],
            'builder' => [
                'num' => (int) ($result->BUILDER_TABLEID ?? null),
                'name' => trim($result->BUILDER_NAME ?? null),
            ]

        ];
        return $item;
    }
}
