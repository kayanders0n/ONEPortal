<?php

namespace Traits\EP;

use Models\EP\TicketCompletedData;

trait TicketCompletedTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getTicketCompletedData(array $filters = [], string $limit = ''): array
    {
        $results = (new TicketCompletedData())->selectTicketCompletedData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTicketCompletedItem($result);
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
    private function buildTicketCompletedItem($result): array
    {
        $item = [
            'item' => [
                'ticket_age' => trim($result->TICKET_AGE),
                'ticket_type' => trim($result->TICKET_TYPE),
                'total' => (int)$result->TOTAL_COUNT,
            ],

        ];
        return $item;
    }
}
