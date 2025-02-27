<?php

namespace Traits\EP;

use Models\EP\BuilderListData;

trait BuilderListTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getBuilderListData(array $filters = [], string $limit = ''): array
    {
        $results = (new BuilderListData())->selectBuilderListData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildBuilderListItem($result);
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
    private function getBuilderListItem(array $filters): ?array
    {
        return $this->getBuilderListData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildBuilderListItem($result): array
    {
        $item = [
            // community
            'builder'   => [
                'item_id'     => (int) $result->SEQNO,
                'num'         => (int) $result->TABLEID,
                'name'        => htmlentities($result->DESCRIPT),
            ],
        ];

        return $item;
    }
}
