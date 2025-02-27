<?php

namespace Traits\EP;

use Models\EP\JobQAData;
use Helpers\Date;

trait JobQATrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobQAData(array $filters = [], string $limit = ''): array
    {

        $results = (new JobQAData())->selectJobQAData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobQAItem($result);
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
    private function buildJobQAItem($result): array
    {
        $item = [
            // data
            'qa'   => [
                'id'              => (int)$result->QA_ID,
                'type'            => (int)$result->QA_TYPE,
                'type_name'       => htmlentities($this->getQATypeName((int)$result->QA_TYPE)),
                'last_visit_id'   => (int)$result->QA_VISITID,
                'last_visit_date' => Date::formatDate($result->VISIT_DATE, 'm/d/Y'),
                'last_group'      => htmlentities($result->LAST_GROUPNAME),
                'last_question'   => (int)$result->LAST_QUESTIONNUM,
            ],
        ];

        return $item;
    }

    private function getQATypeName(int $type) {
        $qa_type = '';
        switch (intval($type)) {
            case 1000: $qa_type = 'Trim'; break;
            case 1001: $qa_type = 'Trim Camera'; break;
            case 1002: $qa_type = 'Top-Out'; break;
            case 1003: $qa_type = 'Gas'; break;
            case 1004: $qa_type = 'Rough-In'; break;
            case 2000: $qa_type = 'Concrete'; break;
            case 3000: $qa_type = 'Framing'; break;
            default: $qa_type = 'Unknown - '.$type;
        }

        return $qa_type;

    }


}
