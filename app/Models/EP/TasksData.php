<?php

namespace Models\EP;

use Core\Model;
use Helpers\DatabaseNoteUpdate;

class TasksData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTasksData(array $filters = [], string $limit = ''): array
    {
        $item_id          = (int) ($filters['item_id'] ?? null);
        $data_type_id     = (int) ($filters['data_type_id'] ?? null);
        $data_id          = (int) ($filters['data_id'] ?? null);
        $type_id          = (int) ($filters['type_id'] ?? null);
        $ticket_type_code = (int) ($filters['ticket_type_code'] ?? null);


        $param_sql = [
            ':ITEM_ID'          => $item_id,
            ':DATA_TYPE_ID'     => $data_type_id,
            ':DATA_ID'          => $data_id,
            ':TYPE_ID'          => $type_id,
            ':TICKET_TYPE_CODE' => $ticket_type_code,
        ];

        $select_sql = 'SELECT
                       TASKS.SEQNO AS TASK_SEQNO,
                       TASKS.TABLEID,
                       TASKS.DESCRIPT,
                       TASKS.SCHEDULESTARTDATE,
                       TASKS.ACTUALFINISHDATE,
                       TASKS.COMPLETED,
                       TASKS.COMMENT,
                       TASKS.ESTTOTALCOST,
                       TASKS.NOTE,
                       TASKS.ASSIGNTOID AS ASSIGNED_ID,
                       ASSIGNED.TABLEID AS ASSIGNED_NUM,
                       ASSIGNED.DESCRIPT AS ASSIGNED_NAME,
                       TASKS.SUBMITBYID AS SUBMITTED_ID,
                       SUBMITTED.TABLEID AS SUBMITTED_NUM,
                       SUBMITTED.DESCRIPT AS SUBMITTED_NAME,
                       TASKS.TICKETTYPECODE ';

        $from_sql   = 'FROM TASKS
                       LEFT OUTER JOIN EMPLOYEE ASSIGNED
                       ON (TASKS.ASSIGNTOID = ASSIGNED.SEQNO)
                       LEFT OUTER JOIN EMPLOYEE SUBMITTED
                       ON (TASKS.SUBMITBYID = SUBMITTED.SEQNO) ';

        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $item_id ? 'AND TASKS.SEQNO IN (:ITEM_ID) ' : '';
        $where_sql  .= $data_type_id ? 'AND TASKS.DATATYPEID IN (:DATA_TYPE_ID) ' : '';
        $where_sql  .= $data_id ? 'AND TASKS.DATASEQNOID = :DATA_ID ' : '';
        $where_sql  .= $ticket_type_code ? 'AND TASKS.TICKETTYPECODE IN (:TICKET_TYPE_CODE) ' : '';
        $where_sql  .= $type_id ? 'AND TASKS.TYPEID IN (:TYPE_ID) ' : '';

        $order_sql  = 'ORDER BY
                       TASKS.CREATEDON DESC,
                       TASKS.TABLEID ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql , $param_sql);

        return [$query, count($query)];
    }

    /**
     * @param $request
     *
     * @return int
     */
    public function updateTasksData($request): int
    {
        $where = ['SEQNO' => (int) $request['item_id']];

        if (!empty($request['note'])) {
            $update = [
                'note_field' => 'NOTE',
                'note'       => html_entity_decode($request['note'], ENT_QUOTES),
                'user_name'  => $request['user_name'],
                'update' => [
                    'OVERRIDE'           => 2,
                    'MODIFIEDBYNAME'     => $request['user_name'],
                    'MODIFIEDON'         => date('m/d/Y H:i:s')
                ]
            ];

            return DatabaseNoteUpdate::update($this->db, 'TASKS', $update, $where);

        } else {
            $update = [
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        return $this->db->update('TASKS', $update, $where);
    }
}
