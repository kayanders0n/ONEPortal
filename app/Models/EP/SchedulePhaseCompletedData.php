<?php

namespace Models\EP;

use Core\Model;

class SchedulePhaseCompletedData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectSchedulePhaseCompletedData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $company_site = $filters['company_site'] ?: null;
        $days_old = (int)$filters['days_old'] ?: null;

        $param_sql = [
        ]; // union query, needs custom binding

        $select1_sql = "SELECT
                        'COMPLETED' AS DATA_TYPE,
                        ITEM.CAPACITYCODE AS PHASE_CODE,
                        ITEM.DESCRIPT AS PHASE_NAME,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $select2_sql = "SELECT
                        'SCHEDULED' AS DATA_TYPE,
                        ITEM.CAPACITYCODE AS PHASE_CODE,
                        ITEM.DESCRIPT AS PHASE_NAME,
                        COUNT(*) AS TOTAL_COUNT " . PHP_EOL;

        $from_sql = "FROM TASKS ITEM
                     LEFT OUTER JOIN PROJECT ON (ITEM.PROJECTID = PROJECT.SEQNO)
                     LEFT OUTER JOIN TASKS ROOT ON (ITEM.PARENTTASKID = ROOT.SEQNO) " . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND ITEM.CAPACITYCODE IN ('4113', '4223', '6023', '8013', '4410', '5610') " . PHP_EOL;
        $where_sql .= "AND ITEM.REQUIREDFINISHDATE IS NOT NULL " . PHP_EOL;
        $where_sql .= 'AND ITEM.DATATYPEID = 29 ' . PHP_EOL;
        $where_sql .= 'AND ROOT.STATUSID = 6628 ' . PHP_EOL; // active
        $where_sql .= 'AND ROOT.COMPLETED = 0 ' . PHP_EOL;
        $where_sql .= 'AND PROJECT.COMPLETED = 0 ' . PHP_EOL;
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
        if ($days_old) {
            $where1_sql = 'AND ITEM.ACTUALFINISHDATE BETWEEN CURRENT_DATE - ' . $days_old . ' AND CURRENT_DATE ' . PHP_EOL;
            $where2_sql = 'AND ITEM.REQUIREDFINISHDATE BETWEEN CURRENT_DATE - ' . $days_old . ' AND CURRENT_DATE ' . PHP_EOL;
        }

        $group_sql = 'GROUP BY 1,2,3 ' . PHP_EOL;
        $union_sql = 'UNION ALL ' . PHP_EOL;
        $order_sql = 'ORDER BY 2,1 ' . PHP_EOL;

        $select_sql = $select1_sql . $from_sql . $where_sql . $where1_sql . $group_sql;
        $select_sql .= $union_sql;
        $select_sql .= $select2_sql . $from_sql . $where_sql . $where2_sql . $group_sql;
        $select_sql .= $order_sql;

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
