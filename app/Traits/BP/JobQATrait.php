<?php

namespace Traits\BP;

use Models\BP\JobQA;
use Helpers\Date;

trait JobQATrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobQA(array $filters = [], string $limit = ''): array
    {
        $results = (new JobQA())->selectJobQA($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobQAItem($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            return $data['results'][0];
        }

        return $data;
    }

    /**
     * @param array $filters
     *
     * @return array|null
     */
    private function getJobQAItem(array $filters): ?array
    {
        return $this->getJobQA($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildJobQAItem($result): array
    {
        $item = [
            // JobQA
            'job_qa'  => [
                'seq_id'       => (int) $result->SEQNO,
                'qa_id'        => (int) $result->TABLEID,
                'job_id'       => (int) $result->JOBID,
                'qa_type'      => (int) $result->QATYPE,
                'is_audit'     => (int) $result->ISAUDIT,
                'project_code' => htmlentities($result->PROJECT_TABLEID),
                'project_name' => htmlentities($result->PROJECT_NAME),
                'created_on'   => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A')
            ],

            // Jobsite
            'jobsite' => [
                'id'      => $result->JOBSITE_IDCODE, // need zero padded left alone, don't cast to int
                'address' => htmlentities($result->JOBSITE_ADDRESS1)
            ],

            // Documents
            'docs'    => [
                'id'       => (int) $result->DOC_SERVERID,
                'filename' => htmlentities($result->DOC_FILENAME)
            ]
        ];

        return $item;
    }
}
