<?php

namespace Models\EP;

use Core\Model;

class EstimatorBillingAdjData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEstimatorBillingAdjData(array $filters = [], string $limit = ''): array
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

        $from_sql = 'FROM JOBBILLINGADJ
                     LEFT OUTER JOIN JOB
                     ON (JOBBILLINGADJ.JOBID = JOB.SEQNO)
                     LEFT OUTER JOIN ENTITY SOURCE
                     ON (JOB.SOURCEENTITYID = SOURCE.SEQNO)
                     LEFT OUTER JOIN ENTITY BUILDER
                     ON (JOB.ENTITYID = BUILDER.SEQNO)
                     LEFT OUTER JOIN GET_PROJECT_ESTIMATOR(JOB.SOURCEENTITYID, JOB.ENTITYID) ESTIMATOR ON (1=1) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= 'AND JOBBILLINGADJ.CREATEDON > CURRENT_DATE - ' . $days_old . ' ' . PHP_EOL;
        $where_sql .= $company_id ? 'AND JOB.SOURCEENTITYID = :COMPANY_ID ' . PHP_EOL : '';
        $where_sql .= 'AND JOBBILLINGADJ.TYPEID IN (286842680, 286842703, 286842858) ' . PHP_EOL;

        $group_sql = 'GROUP BY 1,2 ' . PHP_EOL;

        $order_sql  = 'ORDER BY 1,2 ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
