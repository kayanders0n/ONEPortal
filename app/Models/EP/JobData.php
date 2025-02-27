<?php

namespace Models\EP;

use Core\Model;
use Helpers\DatabaseNoteUpdate;

class JobData extends Model
{
    /**
     * @param int $job_num
     * @return int
     */
    function getJobsSeqnoFromNum(int $job_num): int
    {
        $param_sql = [
            ':JOB_NUM' => $job_num,
        ];

        $select_sql = 'SELECT SEQNO FROM JOB WHERE TABLEID = :JOB_NUM';

        $query = $this->db->selectOne($select_sql, $param_sql);

        return (int) ($query->SEQNO ?? null);
    }

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobsData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) ($filters['item_id'] ?? null);
        $job_num   = (int) ($filters['job_num'] ?? null);
        $count_only = (int) ($filters['count_only'] ?? 0) == 1 ?: false;


        $param_sql = [
            ':JOB_ID'    => $item_id,
            ':JOB_NUM'   => $job_num,
        ];

        $select_sql = 'SELECT  
                       JOB.SEQNO AS JOB_SEQNO, 
                       JOB.TABLEID AS JOB_NUM,
                       JOB.STARTDATE AS JOB_STARTDATE,
                       JOB.COMPLETEDON AS JOB_COMPLETEDON,
                       JOB.HOUSEHAND AS JOB_HOUSEHAND,
                       JOBSITE.SEQNO AS JOBSITE_SEQNO,
                       JOBSITE.TABLEID AS JOBSITE_NUM,
                       JOBSITE.DESCRIPT AS JOBSITE_NAME,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1,
                       JOBSITE.ADDRESS2 AS JOBSITE_ADDRESS2,
                       JOBSITE.CITY AS JOBSITE_CITY,
                       JOBSITE.STATE AS JOBSITE_STATE,
                       JOBSITE.ZIP AS JOBSITE_ZIP,
                       JOBSITE.CLOSEOFESCROWDATE AS JOBSITE_COE,
                       JOBSITE.BLUESTAKENUM AS JOBSITE_BLUESTAKENUM,
                       PROJECT.SEQNO AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       PROJECTPLANS.SEQNO AS PLAN_SEQNO,
                       PROJECTPLANS.TABLEID AS PLAN_NUM,
                       PROJECTPLANS.PLANCODE AS PLAN_CODE,
                       (SELECT FIRST 1 PLANOPTION.DESCRIPT FROM JOBOPTION INNER JOIN PLANOPTION ON (JOBOPTION.OPTIONID = PLANOPTION.SEQNO) WHERE JOBOPTION.JOBID = JOB.SEQNO AND PLANOPTION.ISELEVATION = 1) AS ELEVATION_NAME,
                       JOB.SOURCEENTITYID AS SOURCEENTITY_SEQNO,
                       COMPANY.TABLEID AS SOURCEENTITY_NUM,
                       COMPANY.DESCRIPT AS SOURCEENTITY_NAME,                      
                       JOB.ENTITYID AS BUILDER_SEQNO,
                       BUILDER.TABLEID AS BUILDER_NUM,
                       BUILDER.DESCRIPT AS BUILDER_NAME,
                       JOB.NOTE AS JOB_NOTE, 
                       JOB.CREATEDON,
                       JOB.CREATEBYNAME,
                       JOB.MODIFIEDON,
                       JOB.MODIFIEDBYNAME ';
        $from_sql   = 'FROM JOB 
                       LEFT OUTER JOIN JOBSITE 
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO) 
                       LEFT OUTER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO)
                       LEFT OUTER JOIN PROJECTPLANS
                       ON (JOB.PROJECTPLANID = PROJECTPLANS.SEQNO) 
                       LEFT OUTER JOIN ENTITY COMPANY 
                       ON (JOB.SOURCEENTITYID = COMPANY.SEQNO) 
                       LEFT OUTER JOIN ENTITY BUILDER 
                       ON (JOB.ENTITYID = BUILDER.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $item_id ? 'AND JOB.SEQNO = :JOB_ID ' : '';
        $where_sql  .= $job_num ? 'AND JOB.TABLEID = :JOB_NUM ' : '';
        $order_sql  = 'ORDER BY JOB.TABLEID ';

        $limit_sql = $limit ?: '';

        if (!$count_only) {
            $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = array();
        }
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }

    /**
     * @param int $source_entity_id
     * @param int $builder_id
     * @return array
     */
    function getJobsEstimatorData(int $company_id, int $builder_id): object
    {
        $param_sql = [
            ':COMPANY_ID' => $company_id,
            ':BUILDER_ID' => $builder_id,
        ];

        $select_sql = 'SELECT
                       EMPLOYEE.SEQNO AS EMPLOYEE_SEQNO,
                       EMPLOYEE.TABLEID AS EMPLOYEE_NUM,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEE.EMAIL AS EMPLOYEE_EMAIL ';

        $from_sql = 'FROM TASKS_ENTITY_EMPLOYEE
                     LEFT OUTER JOIN EMPLOYEE
                     ON (TASKS_ENTITY_EMPLOYEE.EMPLOYEEID = EMPLOYEE.SEQNO) ';

        $where_sql = 'WHERE 1 = 1 ';
        $where_sql .= 'AND TASKS_ENTITY_EMPLOYEE.SOURCEENTITYID = :COMPANY_ID ';
        $where_sql .= 'AND ((TASKS_ENTITY_EMPLOYEE.ENTITYID = :BUILDER_ID) OR (TASKS_ENTITY_EMPLOYEE.ENTITYID IS NULL)) ';
        $order_sql = 'ORDER BY TASKS_ENTITY_EMPLOYEE.ENTITYID NULLS LAST ';

        if ($query = $this->db->selectOne($select_sql . $from_sql . $where_sql . $order_sql, $param_sql)) {
            return $query;
        } else {
            return (object)[];
        }
    }

    /**
     * @param int $job_id
     * @return object
     */
    function getJobsProjectConcreteData(int $job_id): object
    {

        $param_sql = [
            ':JOB_ID' => $job_id,
        ];

        $select_sql = 'SELECT
                       PROJECT.CONCRETE_PUMPORDERED,
                       PROJECT.CONCRETE_INSPECTIONCALLED,
                       PROJECT.CONCRETE_PRETREATORDERED,
                       PROJECT.CONCRETE_LASERSCREED,
                       PROJECT.CONCRETE_MIXCODE,
                       CONCRETEVENDOR.DESCRIPT AS CONCRETE_VENDOR,
                       CONCRETEPTCABLEVENDOR.DESCRIPT AS CONCRETE_PT_VENDOR ';

        $from_sql = 'FROM JOB
                     INNER JOIN PROJECT
                     ON (JOB.PROJECTID = PROJECT.SEQNO) 
                     LEFT OUTER JOIN ENTITY CONCRETEVENDOR 
                     ON (PROJECT.CONCRETE_VENDORID = CONCRETEVENDOR.SEQNO)
                     LEFT OUTER JOIN ENTITY CONCRETEPTCABLEVENDOR
                     ON (PROJECT.CONCRETE_PTCABLE_VENDORID = CONCRETEPTCABLEVENDOR.SEQNO) ';

        $where_sql = 'WHERE 1 = 1 ';
        $where_sql .= 'AND JOB.SEQNO = :JOB_ID ';
        $order_sql = 'ORDER BY JOB.SEQNO ';

        $query = $this->db->selectOne($select_sql . $from_sql . $where_sql . $order_sql, $param_sql);

        return $query;

    }

    /**
     * @param int $job_id
     * @return object
     */
    function getJobsPOVendorData(int $job_id, int $to_item_id): object
    {

        $param_sql = [
            ':JOB_ID'       => $job_id,
            ':TO_ITEM_ID'   => $to_item_id
        ];

        $select_sql = 'SELECT                      
                      ENTITY.DESCRIPT AS VENDOR_NAME,
                      ENTITY.EMAIL AS VENDOR_EMAIL,
                      POHEADER.TABLEID AS PO_NUM ';

        $from_sql = 'FROM POHEADERITEMS
                     INNER JOIN ENTITY ON (POHEADERITEMS.ENTITYID = ENTITY.SEQNO)
                     INNER JOIN POHEADER ON (POHEADERITEMS.PURCHORDERID = POHEADER.SEQNO) ';

        $where_sql = 'WHERE 1 = 1 ';
        $where_sql .= 'AND POHEADERITEMS.JOBID = :JOB_ID ';
        $where_sql .= 'AND POHEADERITEMS.PURCHORDERID <> 0 ';
        $where_sql .= 'AND POHEADERITEMS.MATERIALID = :TO_ITEM_ID ';

        $order_sql = 'ORDER BY POHEADERITEMS.SEQNO DESC ';

        if ($query = $this->db->selectOne($select_sql . $from_sql . $where_sql . $order_sql, $param_sql)) {
            return $query;
        } else {
            return (object)[];
        }
    }

    /**
     * @param int $job_id
     * @return object
     */
    function getJobsTakeoffTotalData(int $job_id, int $to_item_id): object
    {
        $param_sql = [
            ':JOB_ID'       => $job_id,
            ':TO_ITEM_ID'   => $to_item_id
        ];

        $select_sql = 'SELECT SUM(UNITS) AS TOTAL_UNITS FROM JOBTAKEOFF WHERE JOBID = :JOB_ID AND TAKEOFFITEMID = :TO_ITEM_ID';

        if ($query = $this->db->selectOne($select_sql, $param_sql)) {
            return $query;
        } else {
            return (object)[];
        }
    }

    /**
     * @param $request
     *
     * @return int
     */
    public function updateJobData($request): int
    {
        $where = ['SEQNO' => (int) $request['item_id']];

        if (!empty($request['note'])) {
            $update = [
                'note_field' => 'NOTE',
                'note'       => html_entity_decode($request['note'],ENT_QUOTES),
                'user_name'  => $request['user_name'],
                'update' => [
                    'MODIFIEDBYNAME'     => $request['user_name'],
                    'MODIFIEDON'         => date('m/d/Y H:i:s')
                ]
            ];

            return DatabaseNoteUpdate::update($this->db, 'JOB', $update, $where);

        } else {
            $update = [
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        return $this->db->update('JOB', $update, $where);
    }
}
