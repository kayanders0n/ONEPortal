<?php

namespace Traits\EP;

use Models\EP\HyphenData;
use Helpers\Date;

trait HyphenTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getHyphenData(array $filters = [], string $limit = ''): array
    {
        $results = (new HyphenData())->selectHyphenData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildHyphenItem($result);
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
    private function getHyphenItem(array $filters): ?array
    {
        return $this->getHyphenData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildHyphenItem($result): array
    {
        $item = [
            // Hyphen
            'hyphen'   => [
                'item_id'             => (int) $result->SEQNO,
                'type_id'             => (int) $result->TYPEID,
                'account_code'        => htmlentities($result->ACCOUNT_EXTERNALID),
                'project_code'        => htmlentities($result->PROJECT_EXTERNALID),
                'project_name'        => htmlentities($result->PROJECT_NAME),
                'jobsite_address'     => htmlentities($result->JOBSITE_ADDRESS),
                'jobsite_lotnum'      => htmlentities($result->JOBSITE_LOTNUM),
                'jobsite_external_id' => htmlentities($result->JOBSITE_EXTERNALID),
                'action_name'         => htmlentities($result->ACTION_NAME),
                'action_date'         => Date::formatDate($result->ACTION_DATE, 'm/d/Y'),
                'action_id'           => htmlentities($result->ACTION_ID),
                'created_on'          => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'processed'           => (int) $result->PROCESSED,
                'processed_on'        => Date::formatDate($result->PROCESSEDON, 'm/d/Y h:i:s A'),
                'deleted'             => (int) $result->DELETED,
                'deleted_on'          => Date::formatDate($result->DELETEDON, 'm/d/Y h:i:s A'),
                'deleted_by'          => $result->DELETEDBYNAME,
                'modified_on'         => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'modified_by'         => $result->MODIFIEDBYNAME,
                'processed_on_raw'    => $result->PROCESSEDON,
            ],

            // Jobsite
            'jobsite'  => [
                'id'      => (int) $result->JOBSITEID,
                'num'     => (int) $result->JOBSITE_TABLEID,
                'name'    => htmlentities($result->JOBSITE_NAME),
                'address' => htmlentities($result->JOBSITE_ADDRESS1),
            ],

            // Task
            'task'     => [
                'id'   => (int) $result->TASKID,
                'num'  => (int) $result->TASK_TABLEID,
                'code' => htmlentities($result->TASK_CAPACITYCODE),
                'name' => htmlentities($result->TASK_NAME),
            ],

            // Document
            'document' => [
                'id'   => (int) $result->PLS_DOCUMENTID,
                'code' => htmlentities($result->DOCUMENT_DESCRIPT),
            ]
        ];

        return $item;
    }
}
