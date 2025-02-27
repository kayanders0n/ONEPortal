<?php

namespace Models\EP;

use Core\Model;

class FramingCommunityInfoData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectFramingCommunityInfoData(array $filters = [], string $limit = ''): array
    {
        $builder_id = (int) ($filters['builder_id'] ?? null);

        $param_sql = [
            ':BUILDER_ID' => $builder_id,
        ];

        $select_sql = "SELECT
                       PROJECT.SEQNO AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       BUILDER.SEQNO AS BUILDER_SEQNO,
                       BUILDER.TABLEID AS BUILDER_NUM,
                       BUILDER.DESCRIPT AS BUILDER_NAME,
                       (SELECT COUNT(*) FROM JOBSITE WHERE JOBSITE.PROJECTID = PROJECT.SEQNO) AS JOBSITE_COUNT,
                       (SELECT COUNT(*) FROM JOB WHERE JOB.SOURCEENTITYID = 21442 AND JOB.PROJECTID = PROJECT.SEQNO) AS JOB_COUNT,
                       (SELECT LINKPROJECT.TABLEID FROM PROJECT LINKPROJECT WHERE LINKPROJECT.SEQNO = PROJECT.LINKFRAMINGPROJECTID) AS FRA_LINKPROJECT_NUM,
                       (SELECT FIRST 1 PROPOSALSUPPLIERS.ENTITYID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 6650) AS HARDWARE_VENDOR_ID,
                       (SELECT FIRST 1 PROPOSALSUPPLIERS.ENTITYID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 33388) AS TRUSS_VENDOR_ID,
                       (SELECT FIRST 1 PROPOSALSUPPLIERS.ENTITYID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 30646) AS LUMBER_VENDOR_ID,
                       (SELECT FIRST 1 VENDOR.TABLEID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 6650) AS HARDWARE_VENDOR_NUM,
                       (SELECT FIRST 1 VENDOR.TABLEID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 33388) AS TRUSS_VENDOR_NUM,
                       (SELECT FIRST 1 VENDOR.TABLEID FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 30646) AS LUMBER_VENDOR_NUM,
                       (SELECT FIRST 1 VENDOR.DESCRIPT FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 6650) AS HARDWARE_VENDOR_NAME,
                       (SELECT FIRST 1 VENDOR.DESCRIPT FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 33388) AS TRUSS_VENDOR_NAME,
                       (SELECT FIRST 1 VENDOR.DESCRIPT FROM PROPOSAL LEFT OUTER JOIN PROPOSALSUPPLIERS ON (PROPOSALSUPPLIERS.PROPOSALID = PROPOSAL.SEQNO) LEFT OUTER JOIN ENTITY VENDOR ON (PROPOSALSUPPLIERS.ENTITYID = VENDOR.SEQNO) WHERE PROPOSAL.PROJECTID = PROJECT.SEQNO AND PROPOSALSUPPLIERS.CATEGORYID = 30646) AS LUMBER_VENDOR_NAME " . PHP_EOL;

        $from_sql = 'FROM PROJECT
                     LEFT OUTER JOIN ENTITY BUILDER
                     ON (PROJECT.OWNERID = BUILDER.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= 'AND PROJECT.COMPLETED = 0 ' . PHP_EOL;
        $where_sql .= 'AND PROJECT.STATUSID IN (6629, 6636) ' . PHP_EOL;
        $where_sql .= 'AND PROJECT.ISFRAMING = 1 ' . PHP_EOL;
        $where_sql .= 'AND PROJECT.TYPEID  NOT IN (6674, 12476479) ' . PHP_EOL;
        $where_sql .= $builder_id ? 'AND BUILDER.SEQNO = :BUILDER_ID ' . PHP_EOL : '';

        $order_sql  = 'ORDER BY 1 ASC ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }
}
