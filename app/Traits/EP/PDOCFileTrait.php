<?php

namespace Traits\EP;

use Models\EP\PDOCFileData;

trait PDOCFileTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getPDOCFileData(array $filters = [], string $limit = ''): array
    {
        $results = (new PDOCFileData())->selectPDOCFileData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildPDOCFileItem($result);
        }

        $data['num_results'] = $results[1];

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildPDOCFileItem($result): array
    {
        $item = [
            // PDOC
            'pdoc' => [
                'old_other_file_count' => (int)$result->OLD_OTHER_FILE_COUNT,
                'old_payroll_file_count' => (int)$result->OLD_PAYROLL_FILE_COUNT,
                'old_payroll_index_file_count' => (int)$result->OLD_PAYROLL_INDEX_FILE_COUNT,
                'old_payroll_review_file_count' => (int)$result->OLD_PAYROLL_REVIEW_FILE_COUNT,
            ],

        ];

        return $item;
    }
}
