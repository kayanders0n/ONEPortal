<?php

namespace Models\EP;

use Core\Model;

class ARProcessQueueData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectARProcessQueueData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
        ];

        $select_sql = "SELECT
                       COUNT(*) AS ITEM_COUNT,
                       PLAR_DOCUMENT.ASSIGNEDEMPLOYEEID AS EMPLOYEE_ID,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME " . PHP_EOL;

        $from_sql = 'FROM PLAR_DOCUMENT
                     LEFT OUTER JOIN EMPLOYEE
                     ON (PLAR_DOCUMENT.ASSIGNEDEMPLOYEEID = EMPLOYEE.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= $company_id ? 'AND PLAR_DOCUMENT.SOURCEENTITYID = :COMPANY_ID ' . PHP_EOL : '';
        $where_sql .= 'AND PLAR_DOCUMENT.DELETED = 0 ' . PHP_EOL;
        $where_sql .= 'AND PLAR_DOCUMENT.ASSIGNEDEMPLOYEEID > 0 ' . PHP_EOL;
        $where_sql .= 'AND PLAR_DOCUMENT.INDEXED = 1 ' . PHP_EOL;
        $where_sql .= 'AND PLAR_DOCUMENT.PROCESSED = 0 ' . PHP_EOL;
        $where_sql .= 'AND PLAR_DOCUMENT.REVIEWED = 0 ' . PHP_EOL;

        $group_sql = 'GROUP BY 2,3 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1,3 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
