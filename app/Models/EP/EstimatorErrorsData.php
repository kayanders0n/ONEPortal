<?php

namespace Models\EP;

use Core\Model;

class EstimatorErrorsData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEstimatorErrorsData(array $filters = [], string $limit = ''): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $days_old = $filters['days_old'] ?: 0;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
        ];

        $select_sql = "SELECT
                       SOURCE.DESCRIPT AS COMPANY_NAME,
                       ESTIMATOR.EMPLOYEE_NAME,
                       COUNT(*) AS ITEM_COUNT " . PHP_EOL;

        $from_sql = 'FROM TASKS
                     LEFT OUTER JOIN JOB
                     ON (JOB.SEQNO = TASKS.DATASEQNOID)
                     LEFT OUTER JOIN ENTITY SOURCE
                     ON (JOB.SOURCEENTITYID = SOURCE.SEQNO)
                     LEFT OUTER JOIN ENTITY BUILDER
                     ON (JOB.ENTITYID = BUILDER.SEQNO)
                     LEFT OUTER JOIN GET_PROJECT_ESTIMATOR(JOB.SOURCEENTITYID, JOB.ENTITYID) ESTIMATOR ON (1=1) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= 'AND TASKS.COMPLETED = 1 ' . PHP_EOL;
        $where_sql .= 'AND TASKS.ACTUALFINISHDATE > CURRENT_DATE - ' . $days_old . ' ' . PHP_EOL;
        $where_sql .= 'AND TASKS.DATATYPEID = 97 ' . PHP_EOL;
        $where_sql .= 'AND TASKS.REASONCODEID = 77275187 ' . PHP_EOL;
        $where_sql .= $company_id ? 'AND JOB.SOURCEENTITYID = :COMPANY_ID ' . PHP_EOL : '';

        $group_sql = 'GROUP BY 1,2 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1,2 ';

        $limit_sql = $limit ?: '';

        //error_log($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql);

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
