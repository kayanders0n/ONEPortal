<?php

namespace Models\EP;

use Core\Model;
use Helpers\Database;

class EmployeeLinkData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEmployeeLinkData(array $filters = [], string $limit = ''): array
    {
        $employee_id = (int)$filters['employee_id'] ?: null;

        $param_sql = [
            ':EMPLOYEE_ID' => $employee_id,
        ];

        $select_sql = 'SELECT
                       EMPLOYEE.SEQNO AS EMPLOYEE_SEQNO, 
                       EMPLOYEE.TABLEID AS EMPLOYEE_NUM,             
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME ';

        $from_sql = 'FROM EMPLOYEELINK
                    INNER JOIN EMPLOYEE ON (EMPLOYEELINK.EMPLOYEEID = EMPLOYEE.SEQNO) ';

        $where_sql = 'WHERE 1=1 ';
        $where_sql .= 'AND EMPLOYEELINK.LINKID = :EMPLOYEE_ID ';
        $where_sql .= 'AND EMPLOYEELINK.SYSTEMTABLEID = 7 ';
        $where_sql .= 'AND EMPLOYEE.COMPLETED = 0 '; /* ACTIVE EMPLOYEE ONLY */

        $group_sql = ' ';
        $order_sql = 'ORDER BY 3,2 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
