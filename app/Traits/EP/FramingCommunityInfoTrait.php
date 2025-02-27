<?php

namespace Traits\EP;

use Models\EP\FramingCommunityInfoData;

trait FramingCommunityInfoTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getFramingCommunityInfoData(array $filters = [], string $limit = ''): array
    {
        $results = (new FramingCommunityInfoData())->selectFramingCommunityInfoData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildFramingCommunityInfoItem($result);
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
    private function buildFramingCommunityInfoItem($result): array
    {
        $jobsite_count = (int)$result->JOBSITE_COUNT;
        $job_count = (int)$result->JOB_COUNT;
        $lots_remaining = $jobsite_count - $job_count;



        $item = [
            'project' => [
                'item_id' => (int)$result->PROJECT_SEQNO,
                'num' => (int)$result->PROJECT_NUM,
                'name' => htmlentities(trim($result->PROJECT_NAME)),
                'linked_proj_num' => (int)$result->FRA_LINKPROJECT_NUM,
                'jobsite_count' => $jobsite_count,
                'job_count' => $job_count,
                'lots_remaining' => $lots_remaining,
            ],

            'builder' => [
                'id' => (int)$result->BUILDER_SEQNO,
                'num' => (int)$result->BUILDER_NUM,
                'name' => htmlentities(trim($result->BUILDER_NAME)),
            ],

            'hardware_company' => [
                'id' => (int)$result->HARDWARE_VENDOR_ID,
                'num' => (int)$result->HARDWARE_VENDOR_NUM,
                'name' => htmlentities(trim($result->HARDWARE_VENDOR_NAME)),
            ],

            'truss_company' => [
                'id' => (int)$result->TRUSS_VENDOR_ID,
                'num' => (int)$result->TRUSS_VENDOR_NUM,
                'name' => htmlentities(trim($result->TRUSS_VENDOR_NAME)),
            ],

            'lumber_company' => [
                'id' => (int)$result->LUMBER_VENDOR_ID,
                'num' => (int)$result->LUMBER_VENDOR_NUM,
                'name' => htmlentities(trim($result->LUMBER_VENDOR_NAME)),
            ]

        ];
        return $item;
    }
}
