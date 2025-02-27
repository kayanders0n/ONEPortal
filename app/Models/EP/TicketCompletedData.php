<?php

namespace Models\EP;

use Core\Model;

class TicketCompletedData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTicketCompletedData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $company_site = $filters['company_site'] ?: null;
        $ticket_type = $filters['ticket_type'] ?: null;

        $param_sql = [
        ]; // union query, needs custom binding

        $select1_sql = "SELECT '030' AS TICKET_AGE,
                        TASKS.TICKETTYPECODE AS TICKET_TYPE,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $select2_sql = "SELECT '060' AS TICKET_AGE,
                        TASKS.TICKETTYPECODE AS TICKET_TYPE,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $select3_sql = "SELECT '090' AS TICKET_AGE,
                        TASKS.TICKETTYPECODE AS TICKET_TYPE,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $select4_sql = "SELECT '120' AS TICKET_AGE,
                        TASKS.TICKETTYPECODE AS TICKET_TYPE,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $from_sql = 'FROM TASKS 
                     LEFT OUTER JOIN PROJECT ON (TASKS.PROJECTID = PROJECT.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= 'AND TASKS.DATATYPEID = 97 ' . PHP_EOL;
        $where_sql .= 'AND TASKS.COMPLETED = 1 ' . PHP_EOL;

        $where_sql .= $company_id ? 'AND TASKS.ENTITYID = ' . $company_id . PHP_EOL : '';
        $where_sql .= $ticket_type ? "AND TASKS.TICKETTYPECODE = '" . $ticket_type . "' " . PHP_EOL : '';

        if ($company_site) {
            switch ($company_site) {
                case 'PHX':
                    $where_sql .= "AND ((PROJECT.GLPREFIX = '') OR (PROJECT.GLPREFIX IS NULL)) " . PHP_EOL;
                    break;
                case 'TUC':
                    $where_sql .= "AND (PROJECT.GLPREFIX = '00020') " . PHP_EOL;
                    break;
            }
        }

        $where1_sql = 'AND TASKS.ACTUALFINISHDATE BETWEEN CURRENT_DATE - 30 AND CURRENT_DATE ' . PHP_EOL;
        $where2_sql = 'AND TASKS.ACTUALFINISHDATE BETWEEN CURRENT_DATE - 60 AND CURRENT_DATE - 31 ' . PHP_EOL;
        $where3_sql = 'AND TASKS.ACTUALFINISHDATE BETWEEN CURRENT_DATE - 90 AND CURRENT_DATE - 61 ' . PHP_EOL;
        $where4_sql = 'AND TASKS.ACTUALFINISHDATE BETWEEN CURRENT_DATE - 120 AND CURRENT_DATE - 91 ' . PHP_EOL;

        $group_sql = 'GROUP BY 1,2 ' . PHP_EOL;
        $union_sql = 'UNION ALL ' . PHP_EOL;
        $order_sql = 'ORDER BY 1 DESC,2 ' . PHP_EOL;

        $select_sql = $select1_sql . $from_sql . $where_sql . $where1_sql . $group_sql;
        $select_sql .= $union_sql;
        $select_sql .= $select2_sql . $from_sql . $where_sql . $where2_sql . $group_sql;
        $select_sql .= $union_sql;
        $select_sql .= $select3_sql . $from_sql . $where_sql . $where3_sql . $group_sql;
        $select_sql .= $union_sql;
        $select_sql .= $select4_sql . $from_sql . $where_sql . $where4_sql . $group_sql;

        $select_sql .= $order_sql;

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
