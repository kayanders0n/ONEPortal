<?php

namespace Models\EP;

use Core\Model;

class EstimatorStartsProductivityData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEstimatorStartsProductivityData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $days_old = $filters['days_old'] ?: 0;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
        ];

        $select_sql = "SELECT
                       COMPANY.DESCRIPT AS COMPANY_NAME,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       CASE POSITION('CHANGE' IN UPPER(PLS_JOBSITE_REVIEW.DESCRIPT)) WHEN 0 THEN 'PHASE' ELSE 'CHANGE' END AS REVIEW_TYPE,
                       COUNT(*) AS ITEM_COUNT " . PHP_EOL;

        $from_sql = 'FROM PLS_JOBSITE_REVIEW
                     INNER JOIN EMPLOYEE
                     ON (PLS_JOBSITE_REVIEW.REVIEWEDBYNAME = EMPLOYEE.LOGINNAME)
                     INNER JOIN EMPLOYEEPAYROLL
                     ON (EMPLOYEE.SEQNO = EMPLOYEEPAYROLL.SEQNO)
                     LEFT OUTER JOIN ENTITY COMPANY
                     ON (PLS_JOBSITE_REVIEW.SOURCEENTITYID = COMPANY.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND EMPLOYEEPAYROLL.SALARYGLSUFFIX = '00270' " . PHP_EOL;
        $where_sql .= 'AND PLS_JOBSITE_REVIEW.REVIEWEDON > CURRENT_DATE - ' . $days_old . ' ' . PHP_EOL;
        $where_sql .= $company_id ? 'AND PLS_JOBSITE_REVIEW.SOURCEENTITYID = :COMPANY_ID ' . PHP_EOL : '';

        $group_sql = 'GROUP BY 1,2,3 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1,2,3,4 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
