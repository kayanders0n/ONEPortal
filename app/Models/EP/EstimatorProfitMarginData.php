<?php

namespace Models\EP;

use Core\Model;

class EstimatorProfitMarginData extends Model
{
    /**
     * @param array $filters
     *
     * @return array
     */
    public function selectEstimatorProfitMarginData(array $filters = []): array
    {
        $company_id = (int)$filters['company_id'] ?: null;
        $project_site = $filters['project_site'] ?: null;

        $param_sql = [
            ':COMPANY_ID' => $company_id,
            ':PROJECT_SITE' => $project_site,
        ];

        if ($project_site == 'ALL') {
            $select_wrap_sql = 'SELECT EMPLOYEE_NAME, ROUND((1-(SUM(COST)/SUM(PRICE)))*100,1) AS PROFIT_MARGIN FROM ( ';
        } else {
            $select_wrap_sql = 'SELECT EMPLOYEE_NAME, PROJ_SITE, ROUND((1-(SUM(COST)/SUM(PRICE)))*100,1) AS PROFIT_MARGIN FROM ( ';
        }

        $select_sql = 'SELECT
                       EST.EMPLOYEE_NAME,
                       (SELECT FIRST 1 COMPANYSITE.PROFITCENTERNAME FROM PROJECTPLANS INNER JOIN COMPANYSITE ON (PROJECTPLANS.COSITEID = COMPANYSITE.SEQNO) WHERE PROJECTID = PROJECT.SEQNO AND SOURCEENTITYID = 5633) AS PROJ_SITE,
                       PROPOSALSUMMARY.COST,
                       PROPOSALSUMMARY.PRICE ' . PHP_EOL;

        $from_sql = 'FROM PROPOSAL
                     INNER JOIN PROJECT
                     ON (PROPOSAL.PROJECTID = PROJECT.SEQNO)
                     INNER JOIN GET_PROJECT_ESTIMATOR(' . $company_id . ', PROJECT.OWNERID) EST ON (1=1)
                     INNER JOIN PROPOSALSUMMARY
                     ON (PROPOSAL.SEQNO = PROPOSALSUMMARY.PROPOSALID) AND
                        (PROPOSAL.ADDENDUMID = PROPOSALSUMMARY.ADDENDUMID)
                     INNER JOIN PROPOSALPLANS
                     ON (PROPOSALSUMMARY.PROPOSALPLANID = PROPOSALPLANS.SEQNO)' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND PROPOSAL.SOURCEENTITYID= " . $company_id . PHP_EOL;
        $where_sql .= "AND PROPOSALPLANS.VOIDADDENDUMID < PROPOSALSUMMARY.ADDENDUMID " . PHP_EOL;
        $where_sql .= "AND ((PROPOSALSUMMARY.COST <> 0) OR (PROPOSALSUMMARY.PRICE <> 0)) " . PHP_EOL;
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

        $order_sql  = 'ORDER BY 1,2 ';

        $query = $this->db->select($select_wrap_sql . $select_sql . $from_sql . $where_sql . $group_sql . $having_sql . $order_sql , $param_sql);

        return [$query, count($query)];
    }
}
