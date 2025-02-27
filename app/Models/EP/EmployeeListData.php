<?php

namespace Models\EP;

use Core\Model;

class EmployeeListData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEmployeeListData(array $filters = [], string $limit = ''): array
    {
        $company_id  = (int)$filters['company_id'] ?: null;
        $employee_type = (string)$filters['employee_type'] ?: null;

        $param_sql = [
            ':COMPANY_ID1'  => $company_id,
            ':COMPANY_ID2'  => $company_id,
        ];

        $select_sql = 'SELECT
                       EMPLOYEE.SEQNO AS EMPLOYEE_SEQNO,
                       EMPLOYEE.TABLEID AS EMPLOYEE_NUM,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEE.FIRSTNAME AS EMPLOYEE_FIRSTNAME,
                       EMPLOYEE.LASTNAME AS EMPLOYEE_LASTNAME,
                       EMPLOYEEPAYROLL.BIRTHDATE AS EMPLOYEE_BIRTHDATE ' . PHP_EOL;

        $from_sql = 'FROM EMPLOYEEPAYROLL
                     INNER JOIN EMPLOYEE 
                     ON (EMPLOYEEPAYROLL.SEQNO = EMPLOYEE.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= 'AND EMPLOYEEPAYROLL.EMPLACTIVE = 1 ' . PHP_EOL;
        $where_sql .= $company_id ? 'AND ((EMPLOYEEPAYROLL.SOURCEENTITYID = :COMPANY_ID1) OR (EMPLOYEE.INPUTID = :COMPANY_ID2)) ': '' . PHP_EOL;

        $order_sql  = 'ORDER BY EMPLOYEE.DESCRIPT '; // default order by

        switch ($employee_type) {
            case EMPLOYEE_ESTIMATOR_TYPE:
                $where_sql .= "AND EMPLOYEEPAYROLL.SALARYGLSUFFIX = '00270' " . PHP_EOL;
                break;
            case EMPLOYEE_BIRTHDAY_TYPE:
                $where_sql .= ' AND DATEADD(DATEDIFF(YEAR,EMPLOYEEPAYROLL.BIRTHDATE, CURRENT_DATE) YEAR TO EMPLOYEEPAYROLL.BIRTHDATE) >= CURRENT_DATE  ';
                $where_sql .= 'AND DATEADD(DATEDIFF(YEAR,EMPLOYEEPAYROLL.BIRTHDATE, CURRENT_DATE) YEAR TO EMPLOYEEPAYROLL.BIRTHDATE) <= CURRENT_DATE + 7 ';
                $where_sql .= "AND ((EMPLOYEEPAYROLL.SALARYGLSUFFIX <> '00150') OR (EMPLOYEEPAYROLL.SALARYGLSUFFIX IS NULL)) ";

                $order_sql = 'ORDER BY EXTRACT(DAY FROM EMPLOYEEPAYROLL.BIRTHDATE), EMPLOYEE.DESCRIPT ';
                break;
        }

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
