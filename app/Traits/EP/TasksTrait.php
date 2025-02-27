<?php

namespace Traits\EP;

use Models\EP\TasksData;
use Helpers\Date;

trait TasksTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getTasksData(array $filters = [], string $limit = ''): array
    {
        $results = (new TasksData())->selectTasksData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildTasksItem($result);
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
    private function getTasksItem(array $filters): ?array
    {
        return $this->getTasksData($filters, 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildTasksItem($result): array
    {
        $item = [
            'task' => [
                'item_id' => (int)$result->TASK_SEQNO,
                'num' => (int)$result->TABLEID,
                'name' => htmlentities(trim($result->DESCRIPT)),
                'schedule_start' => Date::formatDate($result->SCHEDULESTARTDATE, 'm/d/Y'),
                'actual_finish' => Date::formatDate($result->ACTUALFINISHDATE, 'm/d/Y'),
                'completed' => (int)$result->COMPLETED,
                'comment' => htmlentities(trim($result->COMMENT)),
                'est_total' => (int)$result->ESTTOTALCOST,
                'note' => nl2br(htmlentities($result->NOTE)),
                'type_code' => htmlentities(trim($result->TICKETTYPECODE)),
            ],

            'assigned' => [
                'id' => (int)$result->ASSIGNED_ID,
                'num' => (int)$result->ASSIGNED_NUM,
                'name' => htmlentities(trim($result->ASSIGNED_NAME))
            ],

            'submitted' => [
                'id' => (int)$result->SUBMITTED_ID,
                'num' => (int)$result->SUBMITTED_NUM,
                'name' => htmlentities(trim($result->SUBMITTED_NAME))
            ]
        ];

        return $item;
    }
}
