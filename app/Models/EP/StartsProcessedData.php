<?php

namespace Models\EP;

use Core\Model;

class StartsProcessedData extends Model
{
    /**
     * @param array $filters
     *
     * @return array
     */
    public function selectStartsProcessedData(array $filters = []): array
    {

        $type = (string)$filters['type'] ?: null;

        $param_sql = [
        ];

        $select_sql = "SELECT (SUBSTRING(EXTRACT(YEAR FROM PLS_JOBSITE_REVIEW.CREATEDON) FROM 3 FOR 2)) || '-' || RIGHT('0'||TRIM(EXTRACT(MONTH FROM PLS_JOBSITE_REVIEW.CREATEDON)),2) AS RPT_MONTH, 
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME, 
                       EMPLOYEE.SEQNO AS EMPLOYEE_SEQNO,
                       EMPLOYEE.TABLEID AS EMPLOYEE_NUM,
                       EMPLOYEE.COMPLETED AS EMPLOYEE_COMPLETED,
                       EMPLOYEE.CALENDARCOLOR AS EMPLOYEE_COLOR,
                       COUNT(*) AS STARTS_PROCESSED, 
                       SUM(PLS_JOBSITE_REVIEW.STARTERROR) AS ERROR_COUNT " . PHP_EOL;

        $from_sql   = 'FROM PLS_JOBSITE_REVIEW
                       LEFT OUTER JOIN EMPLOYEE
                       ON (PLS_JOBSITE_REVIEW.SOURCEEMPLOYEEID = EMPLOYEE.SEQNO) ' . PHP_EOL;

        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql .= "AND CAST(PLS_JOBSITE_REVIEW.CREATEDON AS DATE) >= DATEADD(-11 MONTH TO CAST(EXTRACT(YEAR FROM CURRENT_DATE)||'-'||(EXTRACT(MONTH FROM CURRENT_DATE))||'-01' AS DATE)) " . PHP_EOL;

        if ($type == 'STARTS') {
            $where_sql .= "AND ((PLS_JOBSITE_REVIEW.DESCRIPT LIKE ('%START%')) OR (PLS_JOBSITE_REVIEW.DESCRIPT LIKE ('%DIG%'))) " . PHP_EOL;
        } else if ($type == 'CHANGES') {
            $where_sql .= "AND (PLS_JOBSITE_REVIEW.DESCRIPT LIKE ('%CHANGE%')) " . PHP_EOL;
        }

        $where_sql .= "AND PLS_JOBSITE_REVIEW.PROCESSED = 1 " . PHP_EOL;
        //$where_sql .= "AND EMPLOYEE.COMPLETED = 0 " . PHP_EOL;

        $group_sql  = 'GROUP BY 1,2,3,4,5,6 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1,2 ';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql , $param_sql);

        return [$query, count($query)];
    }
}
