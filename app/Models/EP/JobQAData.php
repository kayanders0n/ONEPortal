<?php

namespace Models\EP;

use Core\Model;

class JobQAData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobQAData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;
        $qa_type = (int) $filters['qa_type'] ?: 0;
        $audit_data = (int) $filters['audit_data'] == 1 ?: 0;

        $param_sql = [
            ':JOB_ID'     => $job_id,
            ':QA_TYPE'    => $qa_type,
            ':AUDIT_DATA' => $audit_data,
        ];

        $select_sql = 'SELECT FIRST 1
                       JOB_QA.QATYPE AS QA_TYPE, 
                       JOB_QAQUESTION.GROUPNAME  AS LAST_GROUPNAME,
                       MAX(JOB_QAQUESTION.JOB_QAID) AS QA_ID,
                       MAX(JOB_QAVISIT.ACTIVITYDATE) AS VISIT_DATE,
                       MAX(JOB_QAQUESTION.JOB_QAVISITID)  AS QA_VISITID,
                       MAX(JOB_QAQUESTION.QUESTIONNUM) AS LAST_QUESTIONNUM ';

        $from_sql   = 'FROM JOB_QA    
                       INNER JOIN JOB_QAQUESTION ON (JOB_QA.SEQNO = JOB_QAQUESTION.JOB_QAID)
                       INNER JOIN JOB_QAVISIT ON (JOB_QAQUESTION.JOB_QAVISITID = JOB_QAVISIT.SEQNO) ';

        $where_sql = 'WHERE 1=1 ';
        $where_sql .= 'AND JOB_QA.JOBID = :JOB_ID ';
        $where_sql .= 'AND JOB_QA.QATYPE = :QA_TYPE ';
        $where_sql .= 'AND JOB_QA.ISAUDIT = :AUDIT_DATA ';

        $group_sql  = 'GROUP BY JOB_QA.QATYPE, JOB_QAQUESTION.GROUPNAME ';

        $order_sql = 'ORDER BY 5 DESC';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql, $param_sql);

        return [$query, count($query)];
    }
}
