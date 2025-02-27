<?php

namespace Models\EP;

use Core\Model;
use Helpers\DatabaseNoteUpdate;
use Models\EP\JobData;

class WorkOrderData extends Model
{
    /**
     * @param int $wo_num
     * @return int
     */
    function getWorkOrderSeqnoFromNum(int $wo_num): int
    {
        $param_sql = [
            ':WO_NUM' => $wo_num,
        ];

        $select_sql = 'SELECT SEQNO FROM WORKORDER WHERE TABLEID = :WO_NUM';

        $query = $this->db->selectOne($select_sql, $param_sql);

        return (int)$query->SEQNO;
    }

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectWorkOrderData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) ($filters['item_id'] ?? null);
        $wo_num   = (int) ($filters['wo_num'] ?? null);
        $job_id   = (int) ($filters['job_id'] ?? null);
        $job_num   = (int) ($filters['job_num'] ?? null);
        $count_only = (int) (($filters['count_only'] ?? false) == 1);

        if (($job_num !== null) && ($job_id == null)) {
            $job_id = (new JobData())->getJobsSeqnoFromNum($job_num);
        }

        $param_sql = [
            ':WO_ID'    => $item_id,
            ':WO_NUM'   => $wo_num,
            ':JOB_ID'   => $job_id,
        ];

        $select_sql = 'SELECT  
                       WORKORDER.SEQNO AS WO_SEQNO,
                       WORKORDER.TABLEID AS WO_NUM,
                       WORKORDER.DESCRIPT AS WO_NAME,
                       WORKORDER.STARTEDON WO_STARTEDON,
                       WORKORDER.STATUSID AS WO_STATUSID,
                       STATUSCODES.DESCRIPT AS STATUS_NAME,
                       WORKORDER.SOURCEENTITYID AS SOURCEENTITY_SEQNO,
                       COMPANY.TABLEID AS SOURCEENTITY_NUM,
                       COMPANY.DESCRIPT AS SOURCEENTITY_NAME,                      
                       WORKORDER.REFERENCE AS WO_REFERENCE,
                       WORKORDER.COMMENT AS WO_COMMENT,
                       WORKORDER.NOTE AS WO_NOTE,
                       WORKORDER.JOBID AS JOB_SEQNO,
                       JOB.TABLEID AS JOB_NUM,
                       JOB.JOBSITEID AS JOBSITE_SEQNO,
                       JOBSITE.TABLEID AS JOBSITE_NUM,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       JOBSITE.DESCRIPT AS JOBSITE_NAME,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1,
                       JOBSITE.ADDRESS2 AS JOBSITE_ADDRESS2,
                       JOBSITE.CITY AS JOBSITE_CITY,
                       JOBSITE.STATE AS JOBSITE_STATE,
                       JOBSITE.ZIP AS JOBSITE_ZIP,
                       JOB.ENTITYID AS BUILDER_SEQNO,
                       BUILDER.TABLEID AS BUILDER_NUM,
                       BUILDER.DESCRIPT AS BUILDER_NAME,
                       JOB.PROJECTID AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       WORKORDER.CREATEDON,
                       WORKORDER.CREATEBYNAME,
                       WORKORDER.MODIFIEDON,
                       WORKORDER.MODIFIEDBYNAME ';
        $from_sql   = 'FROM WORKORDER  
                       LEFT OUTER JOIN ENTITY COMPANY 
                       ON (WORKORDER.SOURCEENTITYID = COMPANY.SEQNO) 
                       LEFT OUTER JOIN STATUSCODES 
                       ON (WORKORDER.STATUSID = STATUSCODES.SEQNO)
                       LEFT OUTER JOIN JOB
                       ON (WORKORDER.JOBID = JOB.SEQNO) 
                       LEFT OUTER JOIN JOBSITE
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO)
                       LEFT OUTER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO)
                       LEFT OUTER JOIN ENTITY BUILDER 
                       ON (JOB.ENTITYID = BUILDER.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $item_id ? 'AND WORKORDER.SEQNO = :WO_ID ' : '';
        $where_sql  .= $wo_num ? 'AND WORKORDER.TABLEID = :WO_NUM ' : '';
        $where_sql  .= $job_id ? 'AND WORKORDER.JOBID = :JOB_ID ' : '';
        $order_sql  = 'ORDER BY WORKORDER.TABLEID ';

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
     * @param $request
     *
     * @return int
     */
    public function updateWorkOrderData($request): int
    {
        $where = ['SEQNO' => (int) $request['item_id']];

        if (!empty($request['note'])) {
            $update = [
                'note_field' => 'NOTE',
                'note'       => html_entity_decode($request['note'], ENT_QUOTES),
                'user_name'  => $request['user_name'],
                'update' => [
                    'MODIFIEDBYNAME'     => $request['user_name'],
                    'MODIFIEDON'         => date('m/d/Y H:i:s')
                ]
            ];

            return DatabaseNoteUpdate::update($this->db, 'WORKORDER', $update, $where);

        } else {
            $update = [
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        return $this->db->update('WORKORDER', $update, $where);
    }

    /**
     * @param $request
     *
     * @return int
     */
    public function printWorkOrderData($request): int
    {

        $values = [
            'wo_id' => (int)$request['item_id'],
            'wo_num' => (int)$request['wo_num'],
            'company_id' => (int)$request['company_id'],
            'employee_id' => (int)$request['employee_id'],
            'site_code' => (string)$request['site_code'],
            'override_print' => (string)$request['override_print'],
            'user_name' => (string)$request['user_name']
        ];


        $insert = 'INSERT INTO WORKORDER_PRINTQUEUE 
                   (SEQNO, WORKORDERID, WORKORDERTABLEID, SOURCEENTITYID, SOURCEEMPLOYEEID, SITECODE, OVERRIDE_PRINT, 
                   PRINTED, PRINT_COUNT, CREATEBYNAME, CREATEDON) 
                   VALUES(GEN_ID(SYSTEMSEQNONUMBER, 1), :wo_id, :wo_num, :company_id, :employee_id, :site_code, :override_print, 
                   0, 1, :user_name, CURRENT_TIMESTAMP) RETURNING SEQNO';

        $stmt = $this->db->prepare($insert);

        foreach ($values as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $result = $stmt->fetchAll();
        return (int) $result[0]['SEQNO'];

    }
}
