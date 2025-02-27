<?php

namespace Models\EP;

use Core\Model;

class EstimatorLotInventoryData extends Model
{
    /**
     * @param array $filters
     *
     * @return array
     */
    public function selectEstimatorLotInventoryData(array $filters = []): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $project_site = $filters['project_site'] ?: null;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
            ':PROJECT_SITE' => $project_site,
        ];

        if ($project_site == 'ALL') {
            $select_wrap_sql = 'SELECT EMPLOYEE_NAME, SUM(LOT_COUNT) AS LOT_COUNT, SUM(JOB_COUNT) AS CURRENTJOBS_COUNT, SUM(LOT_COUNT-JOB_COUNT) AS LOTSREMAINING_COUNT FROM ( ';
        } else {
            $select_wrap_sql = 'SELECT EMPLOYEE_NAME, PROJ_SITE, SUM(LOT_COUNT) AS LOT_COUNT, SUM(JOB_COUNT) AS CURRENTJOBS_COUNT, SUM(LOT_COUNT-JOB_COUNT) AS LOTSREMAINING_COUNT FROM ( ';
        }

        $select_sql = 'SELECT
                       PROJECT.TABLEID,
                       PROJECT.DESCRIPT,
                       EST.EMPLOYEE_NAME, 
                       (SELECT FIRST 1 COMPANYSITE.PROFITCENTERNAME FROM PROJECTPLANS INNER JOIN COMPANYSITE ON (PROJECTPLANS.COSITEID = COMPANYSITE.SEQNO) WHERE PROJECTID = PROJECT.SEQNO AND SOURCEENTITYID = ' . $company_id . ') AS PROJ_SITE,
                       (SELECT COUNT(*) FROM JOBSITE WHERE PROJECTID = PROJECT.SEQNO) AS LOT_COUNT,
                       (SELECT COUNT(*) FROM JOB WHERE PROJECTID = PROJECT.SEQNO AND SOURCEENTITYID = :COMPANY_ID ) AS JOB_COUNT ' . PHP_EOL;

        $from_sql = 'FROM PROJECT
                     LEFT OUTER JOIN GET_PROJECT_ESTIMATOR( ' . $company_id . ', PROJECT.OWNERID) EST ON (1=1) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND PROJECT.COMPLETED = 0 " . PHP_EOL;
        $where_sql .= "AND PROJECT.STATUSID IN (6629, 6636) " . PHP_EOL;
        $where_sql .= "AND PROJECT.CATEGORYID = 6658 " . PHP_EOL;
        $where_sql .= "AND PROJECT.TYPEID = 6733 " . PHP_EOL;

        switch ($company_id) {
            case PLUMBING_ENTITYID:
                $where_sql .= "AND PROJECT.ISPLUMBING = 1 " . PHP_EOL;
                break;
            case CONCRETE_ENTITYID:
                $where_sql .= "AND PROJECT.ISCONCRETE = 1 " . PHP_EOL;
                break;
            case FRAMING_ENTITYID:
                $where_sql .= "AND PROJECT.ISFRAMING = 1 " . PHP_EOL;
                break;
            case DOORTRIM_ENTITYID:
                $where_sql .= "AND PROJECT.ISDOORANDTRIM = 1 " . PHP_EOL;
                break;
        }

        if ($project_site == 'ALL') {
            $group_sql = ') GROUP BY 1 ' . PHP_EOL;
            $having_sql = '';
        } else {
            $group_sql = ') GROUP BY 1,2 ' . PHP_EOL;
            $having_sql = $project_site ? 'HAVING PROJ_SITE = :PROJECT_SITE ': '' . PHP_EOL;
        }

        $order_sql  = 'ORDER BY 1 ';

        $query = $this->db->select($select_wrap_sql . $select_sql . $from_sql . $where_sql . $group_sql . $having_sql . $order_sql , $param_sql);

        return [$query, count($query)];
    }
}
