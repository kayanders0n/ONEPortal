<?php

namespace Traits\EP;

use Models\EP\PLSJobsiteData;
use Helpers\Date;

trait PLSJobsiteTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getPLSJobsiteData(array $filters = [], string $limit = ''): array
    {
        $results = (new PLSJobsiteData())->selectPLSJobsiteData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildPLSJobsiteItem($result);
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
    private function getPLSJobsiteItem(array $filters): ?array
    {
        return $this->getPLSJobsiteData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildPLSJobsiteItem($result): array
    {
        $item = [
            // PLS
            'pls' => [
                'item_id' => (int)$result->SEQNO,
                'activity_date' => Date::formatDate($result->ACTIVITYDATE, 'm/d/Y h:i:s A'),
                'plu_flag' => (int)$result->ISPLUMBING,
                'con_flag' => (int)$result->ISCONCRETE,
                'fra_flag' => (int)$result->ISFRAMING,
                'created_on' => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
            ],

            // Jobsite
            'jobsite' => [
                'id' => (int)$result->JOBSITEID,
                'num' => (int)$result->JOBSITE_TABLEID,
            ],

        ];
        return $item;
    }
}
