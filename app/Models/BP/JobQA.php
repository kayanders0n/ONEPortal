<?php

namespace Models\BP;

use Core\Model;

class JobQA extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobQA(array $filters = [], string $limit = ''): array
    {
        $item_id    = (int) $filters['item_id'] ?: null;
        $job_id     = (int) $filters['job_id'] ?: 0;
        $builder_id = (int) $filters['builder_id'] ?: 0;

        $qa_type    = (int) 1001;
        $qa_age     = (int) 365;

        $param_sql = [
            ':SEQNO'      => $item_id,
            ':JOB_ID'     => $job_id,
            ':BUILDER_ID' => $builder_id,
            ':QA_TYPE'    => $qa_type,
            ':QA_AGE'     => $qa_age
        ];

        $select_sql = 'SELECT
                       JOB_QA.SEQNO,
                       JOB_QA.TABLEID,
                       JOB_QA.JOBID,
                       JOB_QA.QATYPE,
                       JOB_QA.CREATEDON,
                       JOB_QA.ISAUDIT,
                       PROJECT.TABLEID AS PROJECT_TABLEID,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1,
					             DOCUMENTS.SERVERID AS DOC_SERVERID,
					             DOCUMENTS.DOCUMENTFILE AS DOC_FILENAME ';
        $from_sql   = 'FROM JOB
                       INNER JOIN JOB_QA
                       ON (JOB.SEQNO = JOB_QA.JOBID)
                       INNER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO)
                       INNER JOIN JOBSITE
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO) 
                       INNER JOIN DOCUMENTSLINK
                       ON (JOB_QA.SEQNO = DOCUMENTSLINK.DATASEQNOID) AND
                          (DOCUMENTSLINK.DATATYPEID=761)
                       INNER JOIN DOCUMENTS 
                       ON (DOCUMENTSLINK.DOCUMENTID = DOCUMENTS.SEQNO) AND
                          (DOCUMENTS.DESCRIPT = \'Q/A Video\') ';
        $where_sql  = 'WHERE 1 = 1 ';
//        $where_sql  .= $item_id ? 'AND JOB_QA.SEQNO = :SEQNO ' : '';
//        $where_sql  .= $job_id ? 'AND JOB_QA.JOBID = :JOB_ID ' : '';
        $where_sql .= $qa_type ? 'AND JOB_QA.QATYPE = :QA_TYPE ' : '';
        $where_sql .= $qa_age ? 'AND JOB_QA.CREATEDON > CURRENT_DATE - '.$qa_age : '';
        $where_sql .= $builder_id ? 'AND JOB.ENTITYID = :BUILDER_ID ' : '';
        $order_sql = 'ORDER BY JOB_QA.CREATEDON DESC, 
                       PROJECT.TABLEID,
                       JOBSITE.IDCODE,
                       JOB_QA.QATYPE ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }
}
