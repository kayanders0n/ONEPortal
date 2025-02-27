<?php

namespace Models\EP;

use Core\Model;

class AccountingAROpenBillingData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectAccountingAROpenBillingData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) $filters['item_id'] ?: null;
        $type_code   = $filters['type_code'] ?: null;
        $by_type = (int) $filters['by_type'] == 1 ?: false;
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $param_sql = [
            ':SEQNO'     => $item_id,
            ':TYPE_CODE'   => $type_code,
        ];

        $select_sql = 'SELECT
                       ITEM.SEQNO AS TASK_SEQNO,
                       ITEM.TABLEID AS TASK_TABLEID,
                       ITEM.CAPACITYCODE AS TASK_CAPACITYCODE,
                       ITEM.DESCRIPT AS TASK_NAME,
                       ITEM.DATATABLEID AS JOBSITE_TABLEID,
                       JOBSITE.IDCODE AS LOT_IDCODE,
                       OWNER.DESCRIPT AS BUILDER_NAME,
                       PROJECT.TABLEID AS PROJECT_TABLEID,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       ASSIGNTOENTITY.DESCRIPT AS COMPANY_NAME,
                       ITEM.SCHEDULEFINISHDATE AS TASK_SCHEDULEFINISH,
                       LINKED.DESCRIPT AS LINKED_NAME,
                       LINKED.MODIFIEDONCOMPLETED AS LINKED_MARKEDON,
                       ASSIGNTO.DESCRIPT AS ASSIGNTO_NAME,
                       ITEM.ESTTOTALCOST AS TASK_ESTCOST,
                       ITEM.REFERENCE AS TASK_REFERENCE,
                       ITEM.COMMENT AS TASK_COMMENT ' . PHP_EOL;

        $group_sql = '';

        if ($by_type) {
            $select_sql = 'SELECT
                           ITEM.CAPACITYCODE AS TASK_CAPACITYCODE,
                           ITEM.DESCRIPT AS TASK_NAME,
                           COUNT(*) AS TOTAL_COUNT ' . PHP_EOL;
            $group_sql = 'GROUP BY 1, 2 ';
        }

        $from_sql = 'FROM TASKS ITEM
                     LEFT OUTER JOIN TASKS ROOT ON (ITEM.TASKGROUPID = ROOT.SEQNO) 
                     LEFT OUTER JOIN TASKS LINKED ON (ITEM.LINKEDTASKID = LINKED.SEQNO) 
                     LEFT OUTER JOIN EMPLOYEE ASSIGNTO ON (ITEM.ASSIGNTOID = ASSIGNTO.SEQNO) 
                     LEFT OUTER JOIN ENTITY ASSIGNTOENTITY ON (ITEM.ENTITYID = ASSIGNTOENTITY.SEQNO) 
                     LEFT OUTER JOIN JOBSITE ON (ITEM.DATASEQNOID = JOBSITE.SEQNO) 
                     LEFT OUTER JOIN PROJECT ON (ITEM.PROJECTID = PROJECT.SEQNO)
                     LEFT OUTER JOIN ENTITY OWNER ON (PROJECT.OWNERID = OWNER.SEQNO) ' . PHP_EOL;


        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql .= 'AND ITEM.COMPLETED=0 ' . PHP_EOL;
        $where_sql .= 'AND ROOT.COMPLETED=0 ' . PHP_EOL;
        $where_sql .= 'AND ITEM.DATATYPEID=29 ' . PHP_EOL;
        $where_sql .= 'AND ITEM.CATEGORYID=6643 /*ACCOUNTING*/ ' . PHP_EOL;
        $where_sql .= 'AND ITEM.TYPEID=375156 /*BILLING*/ ' . PHP_EOL;
        $where_sql .= 'AND JOBSITE.SEQNO IS NOT NULL ' . PHP_EOL;
        $where_sql .= "AND ITEM.ASSIGNTOID IN (SELECT EP.SEQNO FROM EMPLOYEEPAYROLL EP WHERE EP.EMPLACTIVE = 1 AND EP.SALARYGLSUFFIX = '00800') /* ACTIVE ACCOUNTING EMPLOYEES */ " . PHP_EOL;
        $where_sql .= "AND ITEM.CAPACITYCODE <> '9007' "; // temporary

        $where_sql  .= $item_id ? 'AND ITEM.SEQNO = :SEQNO ' : '';
        $where_sql  .= $type_code ? 'AND ITEM.CAPACITYCODE = :TYPE_CODE ' : '';

        $order_sql  = 'ORDER BY 1 ';

        $limit_sql = $limit ?: '';

        if (!$count_only) {
            $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = array();
        }

        if (!$by_type) {
            $count_query = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);
            $count = $count_query->num_results;
        } else {
            $count = count($query);
        }

        return [$query, $count];
    }
}
