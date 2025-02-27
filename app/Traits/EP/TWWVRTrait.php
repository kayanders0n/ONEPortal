<?php

namespace Traits\EP;

use Models\EP\TWWVRData;
use Helpers\AwsS3;
use Helpers\Date;

trait TWWVRTrait
{
    /**
     * @param array $filters
     * @param string $limit
     * @param string $scope
     *
     * @return array
     */
    private function getTWWVRData(array $filters = [], string $limit = '', string $scope = ''): array
    {
        $results = (new TWWVRData())->selectTWWVRData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTWWVRItem($result, $scope);
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
     * @param string $scope
     *
     * @return array|null
     */
    private function getTWWVRItem(array $filters, string $scope = ''): ?array
    {
        return $this->getTWWVRData($filters, 'ROWS 1', $scope);
    }

    /**
     * @param object $result
     * @param string $scope
     *
     * @return array
     */
    private function buildTWWVRItem($result, string $scope = ''): array
    {
        $item = [
            // TWWVR
            'twwvr'             => [
                'item_id'          => (int) $result->SEQNO,
                'record_type'      => htmlentities($result->RECORDTYPE),
                'latitude'         => htmlentities($result->LATITUDE),
                'longitude'        => htmlentities($result->LONGITUDE),
                'activity_date'    => Date::formatDate($result->ACTIVITYDATE, 'm/d/Y'),
                'created_on'       => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'processed'        => (int) $result->PROCESSED,
                'processed_on'     => Date::formatDate($result->PROCESSEDON, 'm/d/Y h:i:s A'),
                'deleted'          => (int) $result->DELETED,
                'modified_on'      => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'modified_by'      => $result->MODIFIEDBYNAME,
                'processed_on_raw' => $result->PROCESSEDON,
            ],

            // file
            'file'              => [
                'name'       => htmlentities($result->FILENAME),
                'created_on' => Date::formatDate($result->FILECREATEDON, 'm/d/Y h:i:s A'),
                'size'       => (int) $result->FILESIZE,
                'network'    => htmlentities($result->NETWORK),
                's3_url'     => AwsS3::prepareS3URL($result->FILENAME, 'whittoncompanies.com.twwvr'),
                'file_url'   => HTTPS_SERVER . '/aws-s3/twwvr/url?file=' . htmlentities($result->FILENAME),
            ],

            // Source Entity
            'sourceentity'      => [
                'id'       => (int) $result->SOURCEENTITY_SEQNO,
                'num'      => (int) $result->SOURCEENTITY_NUM,
                'user_num' => (int) $result->SOURCEENTITYTABLEID,
                'name'     => htmlentities($result->SOURCEENTITY_NAME),
            ],

            // Employee
            'employee'          => [
                'id'    => (int) $result->EMPLOYEE_SEQNO,
                'num'   => (int) $result->EMPLOYEE_NUM,
                'name'  => htmlentities($result->EMPLOYEE_NAME),
                'email' => htmlentities($result->EMPLOYEE_EMAIL),
            ],

            // Assigned Employee
            'assigned_employee' => [
                'id'    => (int) $result->ASSIGNEDEMPLOYEE_SEQNO,
                'num'   => (int) $result->ASSIGNEDEMPLOYEE_NUM,
                'name'  => htmlentities($result->ASSIGNEDEMPLOYEE_NAME),
                'email' => htmlentities($result->ASSIGNEDEMPLOYEE_EMAIL),
            ],

            // Job
            'job'               => [
                'id'       => (int) $result->JOBID,
                'num'      => (int) $result->JOB_NUM,
                'user_num' => (int) $result->JOBTABLEID,
                'lot'      => (string) $result->JOBSITE_IDCODE,
            ],

            // Project
            'project'           => [
                'id'   => (int) $result->PROJECT_SEQNO,
                'num'  => (int) $result->PROJECT_NUM,
                'name' => htmlentities($result->PROJECT_NAME),
            ],

            // Task
            'task'              => [
                'id'       => (int) $result->TASKID,
                'num'      => (int) $result->TASK_NUM,
                'user_num' => (int) $result->TASKTABLEID,
                'name'     => htmlentities($result->TASK_NAME),
            ],
        ];

        if (!empty($scope)) {

            switch ($scope) {
                case 'partial':
                    $scope_keys = [
                        'twwvr',
                        'job',
                        'project',
                        'task'
                    ];
                    break;
                default:
                    $scope_keys = [];
                    break;
            }
        }

        if (!empty($scope_keys)) {
            $item = arrayScopeKeys($scope_keys, $item);
        }

        return $item;
    }
}
