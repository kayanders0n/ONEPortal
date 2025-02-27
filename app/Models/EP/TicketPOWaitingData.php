<?php

namespace Models\EP;

use Core\Model;

class TicketPOWaitingData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectTicketPOWaitingData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $company_site = $filters['company_site'] ?: null;
        $days_old = $filters['days_old'] ?: null;
        $by_builder = (int)$filters['by_builder'] == 1 ?: false;

        $param_sql = [
        ]; // union query, needs custom binding

        $select1_sql = "SELECT 
                        'QUOTED' AS DATA_TYPE, " . PHP_EOL;

        $select2_sql = "SELECT
                        'TOTAL' AS DATA_TYPE, " . PHP_EOL;

        $common_sql = '';

        if ($by_builder) {
            $common_sql .= 'BUILDER.TABLEID AS BUILDER_TABLEID,
                            BUILDER.DESCRIPT AS BUILDER_NAME,' . PHP_EOL;
        }

        $common_sql .= 'SUM(ITEM.ESTTOTALCOST) AS TOTAL_COST,
                         COUNT(*) AS TOTAL_COUNT ' . PHP_EOL;

        $from_sql = 'FROM TASKS ITEM
                     LEFT OUTER JOIN PROJECT ON (ITEM.PROJECTID = PROJECT.SEQNO)
                     LEFT OUTER JOIN EMPLOYEEPAYROLL ON (ITEM.SUBMITBYID = EMPLOYEEPAYROLL.SEQNO) ' . PHP_EOL;

        if ($by_builder) {
            $from_sql .= 'LEFT OUTER JOIN JOB ON (ITEM.DATASEQNOID = JOB.SEQNO)
                          LEFT OUTER JOIN ENTITY BUILDER ON (JOB.ENTITYID = BUILDER.SEQNO) ' . PHP_EOL;
        }

        $where_sql =  'WHERE 1 = 1 ';

        $where_sql .= 'AND ITEM.DATATYPEID = 97 ' . PHP_EOL;
        $where_sql .= "AND ITEM.TICKETTYPECODE = 'PO' " . PHP_EOL;
        $where_sql .= 'AND ITEM.TYPEID = 1084076 ' . PHP_EOL;
        $where_sql .= 'AND ITEM.STATUSID NOT IN(79728307,88893452,75245038) /* Internal P/O, Cancelled, Duplicate */ ' . PHP_EOL;
        $where_sql .= 'AND PROJECT.COMPLETED = 0 ' . PHP_EOL;
        $where_sql .= "AND EMPLOYEEPAYROLL.SALARYGLSUFFIX = '00100' " . PHP_EOL;

        $where_sql .= $company_id ? 'AND ITEM.ENTITYID = ' . $company_id . PHP_EOL : '';

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

        $where_sql .= $days_old ? 'AND ITEM.CREATEDON > CURRENT_DATE - ' . $days_old .' ' . PHP_EOL : '';

        $where1_sql = 'AND ITEM.OFFICESTATUSID IN(36742080) /* quoted */ ' . PHP_EOL;
        $where2_sql = '';

        if ($by_builder) {
            $group_sql = 'GROUP BY 1,2,3 ' . PHP_EOL;
        } else {
            $group_sql = 'GROUP BY 1 ' . PHP_EOL;
        }

        $union_sql = 'UNION ALL ' . PHP_EOL;

        if ($by_builder) {
            $order_sql = 'ORDER BY 1, 4 DESC ' . PHP_EOL;
        } else {
            $order_sql = 'ORDER BY 1 ' . PHP_EOL;
        }

        $select_sql = $select1_sql . $common_sql . $from_sql . $where_sql . $where1_sql . $group_sql;
        if (!$by_builder) { // only show the QUOTED if by builder
            $select_sql .= $union_sql;
            $select_sql .= $select2_sql . $common_sql . $from_sql . $where_sql . $where2_sql . $group_sql;
        }

        $select_sql .= $order_sql;

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
