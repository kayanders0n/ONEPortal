<?php

namespace Models\EP;

use Core\Model;

class EstimatorStartsProcessedLateData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEstimatorStartsProcessedLateData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $days_old = $filters['days_old'] ?: 0;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
        ];

        $select_sql = "SELECT
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       ENTITY.DESCRIPT AS COMPANY_NAME,
                       COUNT(*) AS ITEM_COUNT,
                       AVG((CAST(PLS_JOBSITE_REVIEW.REVIEWEDON AS DATE)) - (PLS_JOBSITE_REVIEW.ACTIVITYDATE)) AS AVERAGE_DAYS_LATE " . PHP_EOL;

        $from_sql = 'FROM PLS_JOBSITE_REVIEW
                     LEFT OUTER JOIN EMPLOYEE
                     ON (PLS_JOBSITE_REVIEW.REVIEWEDBYNAME = EMPLOYEE.LOGINNAME)
                     LEFT OUTER JOIN EMPLOYEEPAYROLL
                     ON (EMPLOYEE.SEQNO = EMPLOYEEPAYROLL.SEQNO)
                     LEFT OUTER JOIN ENTITY
                     ON (EMPLOYEEPAYROLL.SOURCEENTITYID = ENTITY.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND POSITION('CHANGE' IN UPPER(PLS_JOBSITE_REVIEW.DESCRIPT)) = 0 " . PHP_EOL; // only starts not changes
        $where_sql .= $company_id ? 'AND PLS_JOBSITE_REVIEW.REVIEWED = 1 ' . PHP_EOL : '';
        $where_sql .= $company_id ? 'AND CAST(PLS_JOBSITE_REVIEW.REVIEWEDON AS DATE) > PLS_JOBSITE_REVIEW.ACTIVITYDATE ' . PHP_EOL : '';
        $where_sql .= 'AND PLS_JOBSITE_REVIEW.REVIEWEDON > CURRENT_DATE - ' . $days_old . ' ' . PHP_EOL;
        $where_sql .= $company_id ? 'AND EMPLOYEEPAYROLL.SOURCEENTITYID = :COMPANY_ID ' . PHP_EOL : '';

        $group_sql = 'GROUP BY 1,2 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
