<?php

namespace Models\EP;

use Core\Model;
use Helpers\Database;

class TWWVRProductivityData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTWWVRProductivityData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $company_site = $filters['company_site'] ?: null;
        $days_old = (int)$filters['days_old'] ?: false;


        $param_sql = [
            ':COMPANY_ID' => $company_id,
            ':COMPANY_SITE' => $company_site,
        ];

        $select_sql = 'SELECT
                       EMPLOYEE.TABLEID AS EMPLOYEE_TABLEID,             
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEEPAYROLL.SALARYGLPREFIX AS EMPLOYEE_LOCATION,
                       SUM(TWWVR_ITEM.FILESIZE)/1024/1024 AS TOTAL_MEG,
                       COUNT(*) AS TOTAL_COUNT ';

        $from_sql = 'FROM TWWVR_ITEM 
                    LEFT OUTER JOIN EMPLOYEE ON (TWWVR_ITEM.EMPLOYEEID = EMPLOYEE.SEQNO)
                    LEFT OUTER JOIN EMPLOYEEPAYROLL ON (EMPLOYEE.SEQNO = EMPLOYEEPAYROLL.SEQNO) ';
        $where_sql = 'WHERE 1=1 ';
        $where_sql .= 'AND TWWVR_ITEM.FILESIZE > 1 ';
        $where_sql .= 'AND TWWVR_ITEM.DELETED = 0 ';
        $where_sql .= "AND EMPLOYEEPAYROLL.SALARYGLPREFIX NOT IN ('00010', '00060') ";

        $where_sql .= $company_id ? 'AND TWWVR_ITEM.SOURCEENTITYID = :COMPANY_ID ' : '';
        $where_sql .= $company_site ? 'AND EMPLOYEEPAYROLL.SALARYGLPREFIX = :COMPANY_SITE ' : '';
        $where_sql .= $days_old ? 'AND TWWVR_ITEM.CREATEDON > CURRENT_DATE - ' . $days_old . ' ' : '';

        $group_sql = 'GROUP BY 1,2,3 ';
        $order_sql = 'ORDER BY 5 DESC,3,2 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }



    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTWWVRProductivityTicketData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int) ($filters['company_id'] ?? null);
        $company_site = ($filters['company_site'] ?? null);
        $days_old = (int) ($filters['days_old'] ?? null);
        $by_builder = (int) ($filters['by_builder'] ?? null) == 1 ?: false;

        $param_sql = [
        ]; // union query, needs custom binding

        $select1_sql = "SELECT
                        'TWWVR' AS DATA_TYPE,
                        COUNT(*) AS TOTAL_COUNT
                        FROM TWWVR_ITEM
                        INNER JOIN TASKS ON (TWWVR_ITEM.TASKID = TASKS.SEQNO)
                        INNER JOIN JOB ON (TWWVR_ITEM.JOBID = JOB.SEQNO) " . PHP_EOL;

        $where1_sql  = 'WHERE 1=1 ' . PHP_EOL;
        $where1_sql .= 'AND TWWVR_ITEM.TASKID > 0 ' . PHP_EOL;
        $where1_sql .= "AND TASKS.TICKETTYPECODE IN ('PHASE') " . PHP_EOL;
        $where1_sql .= $company_id ? 'AND TWWVR_ITEM.SOURCEENTITYID = ' . $company_id . PHP_EOL : '';
        $where1_sql .= $days_old ? 'AND TWWVR_ITEM.CREATEDON > CURRENT_DATE - ' . $days_old .' ' . PHP_EOL : '';

        $select2_sql = "SELECT
                        'TICKETS' AS DATA_TYPE,
                        COUNT(*) AS TOTAL_COUNT
                        FROM TASKS
                        INNER JOIN JOB ON (TASKS.DATASEQNOID = JOB.SEQNO) " . PHP_EOL;
        $where2_sql  = 'WHERE 1=1 ' . PHP_EOL;
        $where2_sql .= 'AND TASKS.DATATYPEID = 97 ' . PHP_EOL;
        $where2_sql .= 'AND TASKS.COMPLETED = 1 '  . PHP_EOL;
        $where2_sql .= "AND TASKS.TICKETTYPECODE IN ('PHASE') "  . PHP_EOL;
        $where2_sql .= $company_id ? 'AND JOB.SOURCEENTITYID = ' . $company_id . PHP_EOL : '';
        $where2_sql .= $days_old ? 'AND TASKS.ACTUALFINISHDATE > CURRENT_DATE - ' . $days_old .' ' . PHP_EOL : '';

        $where_sql = '';
        if ($company_site) {
            switch ($company_site) {
                case 'PHX':
                    $where_sql .= "AND JOB.GLPREFIX IN ('00010', '00030', '00040') " . PHP_EOL;
                    break;
                case 'TUC':
                    $where_sql .= "AND JOB.GLPREFIX IN ('00020') " . PHP_EOL;
                    break;
            }
        }

        $groupby_sql = 'GROUP BY 1 ' . PHP_EOL;

        $union_sql = 'UNION ALL ' . PHP_EOL;

        $order_sql = 'ORDER BY 1, 2 ' . PHP_EOL;

        $select_sql = $select1_sql . $where1_sql . $where_sql . $groupby_sql. $union_sql . $select2_sql . $where2_sql . $where_sql . $groupby_sql . $order_sql;

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
