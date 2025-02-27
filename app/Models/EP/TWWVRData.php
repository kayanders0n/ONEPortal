<?php

namespace Models\EP;

use Core\Model;
use Helpers\Date;

class TWWVRData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTWWVRData(array $filters = [], string $limit = ''): array
    {
        $item_id     = (int) ($filters['item_id'] ?? null);
        $employee_id = (int) ($filters['employee_id'] ?? 0);
        $company_id  = (int) ($filters['company_id'] ?? 0);
        $site        = (int) ($filters['site'] ?? 0);
        $processed   = (int) ($filters['processed'] ?? 0);
        $deleted     = (int) ($filters['deleted'] ??  0);
        $date        = (string) ($filters['date'] ?? '');
        $super_only  = (int) ($filters['super_only'] ?? 0);
        $field_only  = (int) ($filters['field_only'] ?? 0);
        $not_linked  = (int) ($filters['not_linked'] ?? 0);
        $last_24h    = (int) ($filters['last_24h'] ?? 0);
        $count_only  = (int) ($filters['count_only'] ?? 0) == 1 ?: false;

        if (Date::isDate($date)) {
            $date = date('m/d/Y', strtotime($date));
        }

        $param_sql = [
            ':SEQNO'       => $item_id,
            ':EMPLOYEE_ID' => $employee_id,
            ':COMPANY_ID'  => $company_id,
            ':PROCESSED'   => $processed,
            ':DELETED'     => $deleted,
            ':DATE'        => $date
        ];

        $select_sql = 'SELECT
                       TWWVR_ITEM.SEQNO,
                       TWWVR_ITEM.SOURCEENTITYID AS SOURCEENTITY_SEQNO,                       
                       TWWVR_ITEM.SOURCEENTITYTABLEID,
                       COMPANY.TABLEID AS SOURCEENTITY_NUM,
                       COMPANY.DESCRIPT AS SOURCEENTITY_NAME,
                       TWWVR_ITEM.EMPLOYEEID AS EMPLOYEE_SEQNO,
                       TWWVR_ITEM.EMPLOYEETABLEID AS EMPLOYEE_NUM,
                       SOURCEEMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       SOURCEEMPLOYEE.EMAIL AS EMPLOYEE_EMAIL,                       
                       TWWVR_ITEM.ASSIGNEDEMPLOYEEID AS ASSIGNEDEMPLOYEE_SEQNO,
                       ASSIGNEDEMPLOYEE.TABLEID AS ASSIGNEDEMPLOYEE_NUM,
                       ASSIGNEDEMPLOYEE.DESCRIPT AS ASSIGNEDEMPLOYEE_NAME,
                       ASSIGNEDEMPLOYEE.EMAIL AS ASSIGNEDEMPLOYEE_EMAIL,                       
                       TWWVR_ITEM.JOBID,
                       TWWVR_ITEM.JOBTABLEID,                       
                       JOB.TABLEID AS JOB_NUM,
                       PROJECT.SEQNO AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       TWWVR_ITEM.TASKID,
                       TWWVR_ITEM.TASKTABLEID,
                       TASKS.TABLEID AS TASK_NUM,
                       TASKS.DESCRIPT AS TASK_NAME,
                       TWWVR_ITEM.RECORDTYPE,
                       TWWVR_ITEM.LATITUDE,
                       TWWVR_ITEM.LONGITUDE,
                       TWWVR_ITEM.FILENAME,
                       TWWVR_ITEM.FILECREATEDON,
                       TWWVR_ITEM.FILESIZE,
                       TWWVR_ITEM.NETWORK,
                       TWWVR_ITEM.PROCESSED,
                       TWWVR_ITEM.PROCESSEDON,
                       TWWVR_ITEM.DELETED,
                       TWWVR_ITEM.ACTIVITYDATE,                       
                       TWWVR_ITEM.CREATEDON,
                       TWWVR_ITEM.CREATEDBYNAME,
                       TWWVR_ITEM.MODIFIEDON,
                       TWWVR_ITEM.MODIFIEDBYNAME ';

        $from_sql  = 'FROM TWWVR_ITEM
                       LEFT OUTER JOIN EMPLOYEE SOURCEEMPLOYEE  
                       ON (TWWVR_ITEM.EMPLOYEEID = SOURCEEMPLOYEE.SEQNO)
                       LEFT OUTER JOIN EMPLOYEE ASSIGNEDEMPLOYEE  
                       ON (TWWVR_ITEM.ASSIGNEDEMPLOYEEID = ASSIGNEDEMPLOYEE.SEQNO)
                       LEFT OUTER JOIN EMPLOYEEPAYROLL ASGEMPPAYROLL
                       ON (ASSIGNEDEMPLOYEE.SEQNO = ASGEMPPAYROLL.SEQNO) 
                       LEFT OUTER JOIN ENTITY COMPANY
                       ON (TWWVR_ITEM.SOURCEENTITYID = COMPANY.SEQNO)
                       LEFT OUTER JOIN JOB
                       ON (TWWVR_ITEM.JOBID = JOB.SEQNO)
                       LEFT OUTER JOIN JOBSITE
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO)
                       LEFT OUTER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO)
                       LEFT OUTER JOIN TASKS
                       ON (TWWVR_ITEM.TASKID = TASKS.SEQNO) ';
        $where_sql = 'WHERE 1 = 1 ';
        $where_sql .= (!$item_id && !$last_24h) ? 'AND TWWVR_ITEM.PROCESSED = :PROCESSED ' : '';
        $where_sql .= !$item_id ? 'AND TWWVR_ITEM.DELETED = :DELETED ' : '';
        $where_sql .= $item_id ? 'AND TWWVR_ITEM.SEQNO = :SEQNO ' : '';
        $where_sql .= $employee_id ? 'AND TWWVR_ITEM.ASSIGNEDEMPLOYEEID = :EMPLOYEE_ID ' : '';
        $where_sql .= $company_id ? 'AND TWWVR_ITEM.SOURCEENTITYID = :COMPANY_ID ' : '';
        $where_sql .= $not_linked ? 'AND TWWVR_ITEM.JOBID = 0 ' : '';
        $where_sql .= $field_only ? "AND TWWVR_ITEM.DEVICEIDCODE <> '' " : '';
        $where_sql .= $super_only ? "AND TWWVR_ITEM.DEVICEIDCODE = '' " : '';
        $where_sql .= $last_24h ? 'AND TWWVR_ITEM.CREATEDON >= DATEADD(-24 HOUR to localtimestamp) ' : '';

        switch ($site) {
            case 10:
                $where_sql .= "AND ASGEMPPAYROLL.SALARYGLPREFIX = '00010' ";
                break; // mesa
            case 20:
                $where_sql .= "AND ASGEMPPAYROLL.SALARYGLPREFIX = '00020' ";
                break; // tucson
            case 30:
                $where_sql .= "AND ASGEMPPAYROLL.SALARYGLPREFIX = '00030' ";
                break; // west
            case 40:
                $where_sql .= "AND ASGEMPPAYROLL.SALARYGLPREFIX = '00040' ";
                break; // east
            case 100:
                $where_sql .= "AND ASGEMPPAYROLL.SALARYGLPREFIX IN('00010', '00030', '00040') ";
                break; // all phoenix
        }

        $order_sql = 'ORDER BY TWWVR_ITEM.MODIFIEDON DESC ';

        if ($processed == 1) {
            $where_sql .= 'AND CAST(TWWVR_ITEM.PROCESSEDON AS DATE) = :DATE ';
            $order_sql = 'ORDER BY TWWVR_ITEM.PROCESSEDON DESC';
        }

        if ($deleted == 1) {
            $where_sql .= 'AND CAST(TWWVR_ITEM.MODIFIEDON AS DATE) = :DATE ';
            $order_sql = 'ORDER BY TWWVR_ITEM.MODIFIEDON DESC ';
        }

        $limit_sql = $limit ?: '';

        if (!$count_only) {
            $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = [];
        }
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }

    /**
     * @param $request
     *
     * @return int
     */
    public function updateTWWVRData($request): int
    {
        if (!empty($request['delete_item']) && $request['delete_item'] == 'YES') {
            $update = [
                'DELETED'        => 1,
                'MODIFIEDBYNAME' => $request['user_name'],
                'MODIFIEDON'     => date('m/d/Y H:i:s')
            ];
        } else if (!empty($request['process_item']) && $request['process_item'] == 'YES') {
            $update = [
                'PROCESSED'       => 1,
                'PROCESSEDBYNAME' => $request['user_name'],
                'PROCESSEDON'     => date('m/d/Y H:i:s'),
                'WORKPERCENTDONE' => floatval($request['work_percent_done']),
                'NOTE'            => trim($request['note']),
            ];
        } else {
            $update = [
                'TASKID'         => 0,
                'TASKTABLEID'    => $request['task_num'],
                'JOBID'          => 0,
                'JOBTABLEID'     => $request['job_num'],
                'MODIFIEDBYNAME' => $request['user_name'],
                'MODIFIEDON'     => date('m/d/Y H:i:s')
            ];
        }

        $where = ['SEQNO' => (int) $request['item_id']];

        return $this->db->update('TWWVR_ITEM', $update, $where);
    }
}
