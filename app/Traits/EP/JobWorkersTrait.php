<?php

namespace Traits\EP;

use Models\EP\JobWorkersData;

trait JobWorkersTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobWorkersData(array $filters = [], string $limit = ''): array
    {
        $results = (new JobWorkersData())->selectJobWorkersData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobWorkersItem($result);
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
    private function buildJobWorkersItem($result): array
    {
        $item = [
            // data
            'worker'   => [
                'phase'   => htmlentities($result->WORK_PHASENAME . ($result->WORK_PHASECODE ? ' (' . $result->WORK_PHASECODE . ')' : '')),
                'name'    => htmlentities($result->WORKER_NUM . '- '. $result->WORKER_NAME),
            ],
        ];

        return $item;
    }
}
