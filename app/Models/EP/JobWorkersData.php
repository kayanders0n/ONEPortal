<?php

namespace Models\EP;

use Core\Model;

class JobWorkersData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobWorkersData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;

        $param_sql = [
            ':JOB_ID1' => $job_id,
            ':JOB_ID2' => $job_id,
        ];

        // unions require each field to be bound to a unique parameter even though they are the same in this case

        $union_sql = 'SELECT
                      EMPLOYEELINK.REFERENCE AS WORK_PHASECODE,
                      (SELECT FIRST 1 DESCRIPT FROM TASKTEMPLATEITEM WHERE CAPACITYCODE = EMPLOYEELINK.REFERENCE) AS WORK_PHASENAME,
                      EMPLOYEE.TABLEID AS WORKER_NUM,
                      EMPLOYEE.DESCRIPT AS WORKER_NAME
                      FROM JOB
                      INNER JOIN EMPLOYEELINK
                      ON (JOB.JOBSITEID = EMPLOYEELINK.LINKID) AND
                         (JOB.SOURCEENTITYID = EMPLOYEELINK.ENTITYID) AND
                         (EMPLOYEELINK.SYSTEMTABLEID = 29)
                      LEFT OUTER JOIN EMPLOYEE
                      ON (EMPLOYEELINK.EMPLOYEEID = EMPLOYEE.SEQNO)
                      LEFT OUTER JOIN ENTITY
                      ON (EMPLOYEELINK.ENTITYID = ENTITY.SEQNO)                      
                      WHERE                                            
                      JOB.SEQNO = :JOB_ID1
                      UNION ALL
                      SELECT DISTINCT
                      NULL AS WORK_PHASECODE,
                      \'Sub Contractor\' AS WORK_PHASENAME,
                      ENTITY.TABLEID AS WORKER_NUM,
                      ENTITY.DESCRIPT AS WORKER_NAME
                      FROM POHEADERITEMS
                      LEFT OUTER JOIN POHEADER ON (POHEADERITEMS.PURCHORDERID = POHEADER.SEQNO)
                      LEFT OUTER JOIN ENTITY ON (POHEADER.ENTITYID = ENTITY.SEQNO)
                      WHERE
                      POHEADERITEMS.JOBID = :JOB_ID2 AND
                      MATERIALID = 9030170 /*SUBCONTRACTOR LABOR*/                      
                      ORDER BY 1 NULLS FIRST';

        $query = $this->db->select($union_sql, $param_sql);

        return [$query, count($query)];
    }
}
