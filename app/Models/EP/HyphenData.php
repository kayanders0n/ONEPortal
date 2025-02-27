<?php

namespace Models\EP;

use Core\Model;
use Helpers\Date;

class HyphenData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectHyphenData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) ($filters['item_id'] ?? null);
        $type_id   = (int) ($filters['type_id'] ?? 0);
        $processed = (int) ($filters['processed'] ?? 0);
        $deleted   = (int) ($filters['deleted'] ?? 0);
        $date      = (string) ($filters['date'] ?? '');
        $count_only = (int) ($filters['count_only'] ?? 0) == 1 ?: false;

        if (Date::isDate($date)) {
            $date = date('m/d/Y', strtotime($date));
        }

        $param_sql = [
            ':SEQNO'     => $item_id,
            ':TYPE_ID'   => $type_id,
            ':PROCESSED' => $processed,
            ':DELETED'   => $deleted,
            ':DATE'      => $date
        ];

        $select_sql = 'SELECT  
                       HYPHEN_DATA.SEQNO, 
                       HYPHEN_DATA.TYPEID,
                       HYPHEN_DATA.TASKID,
                       HYPHEN_DATA.JOBSITEID,
                       HYPHEN_DATA.PLS_DOCUMENTID,
                       HYPHEN_DATA.ACCOUNT_EXTERNALID,
                       HYPHEN_DATA.PROJECT_EXTERNALID,
                       HYPHEN_DATA.PROJECT_NAME,
                       HYPHEN_DATA.JOBSITE_ADDRESS,
                       HYPHEN_DATA.JOBSITE_LOTNUM,
                       HYPHEN_DATA.JOBSITE_EXTERNALID,
                       HYPHEN_DATA.ACTION_NAME,
                       HYPHEN_DATA.ACTION_DATE,
                       HYPHEN_DATA.ACTION_ID,
                       HYPHEN_DATA.PROCESSED,
                       HYPHEN_DATA.PROCESSEDON,
                       HYPHEN_DATA.DELETED,
                       HYPHEN_DATA.DELETEDON,
                       HYPHEN_DATA.DELETEDBYNAME,
                       HYPHEN_DATA.OVERRIDE,
                       HYPHEN_DATA.CREATEDON,
                       HYPHEN_DATA.CREATEDBYNAME,
                       HYPHEN_DATA.MODIFIEDBYNAME,
                       HYPHEN_DATA.MODIFIEDON,
                       TASKS.TABLEID AS TASK_TABLEID,
                       TASKS.DESCRIPT AS TASK_NAME,
                       TASKS.CAPACITYCODE AS TASK_CAPACITYCODE,
                       JOBSITE.TABLEID AS JOBSITE_TABLEID,
                       JOBSITE.DESCRIPT AS JOBSITE_NAME,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1,
                       PLS_DOCUMENT.DESCRIPT AS DOCUMENT_DESCRIPT ';
        $from_sql   = 'FROM HYPHEN_DATA 
                       LEFT OUTER JOIN TASKS 
                       ON (HYPHEN_DATA.TASKID = TASKS.SEQNO) 
                       LEFT OUTER JOIN JOBSITE 
                       ON (HYPHEN_DATA.JOBSITEID = JOBSITE.SEQNO) 
                       LEFT OUTER JOIN PLS_DOCUMENT 
                       ON (HYPHEN_DATA.PLS_DOCUMENTID = PLS_DOCUMENT.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= !$item_id ? 'AND HYPHEN_DATA.PROCESSED = :PROCESSED ' : '';
        $where_sql  .= !$item_id ? 'AND HYPHEN_DATA.DELETED = :DELETED ' : '';
        $where_sql  .= $item_id ? 'AND HYPHEN_DATA.SEQNO = :SEQNO ' : '';
        $where_sql  .= $type_id ? 'AND HYPHEN_DATA.TYPEID = :TYPE_ID ' : '';
        $order_sql  = 'ORDER BY HYPHEN_DATA.MODIFIEDON DESC ';

        if ($processed == 1) {
            $where_sql .= 'AND CAST(HYPHEN_DATA.PROCESSEDON AS DATE) = :DATE ';
            $order_sql = 'ORDER BY HYPHEN_DATA.PROCESSEDON DESC';
        }

        if ($deleted == 1) {
            $where_sql .= 'AND CAST(HYPHEN_DATA.DELETEDON AS DATE) = :DATE ';
            $order_sql = 'ORDER BY HYPHEN_DATA.DELETEDON DESC ';
        }

        $limit_sql = $limit ?: '';

        if (!$count_only) {
            $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = array();
        }
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }

    /**
     * @param $request
     *
     * @return int
     */
    public function updateHyphenData($request): int
    {
        if (!empty($request['delete_item']) && $request['delete_item'] == 'YES') {
            $update = [
                'DELETED'       => 1,
                'DELETEDBYNAME' => $request['user_name'],
                'DELETEDON'     => date('m/d/Y H:i:s')
            ];
        } else {
            $update = [
                'ACCOUNT_EXTERNALID' => $request['account_code'],
                'PROJECT_EXTERNALID' => $request['project_code'],
                'JOBSITE_LOTNUM'     => $request['jobsite_lotnum'],
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        $where = ['SEQNO' => (int) $request['item_id']];

        return $this->db->update('HYPHEN_DATA', $update, $where);
    }
}
