<?php

namespace Models\EP;

use Core\Model;

class JobOptionsData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobOptionsData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;

        $param_sql = [
            ':JOB_ID1' => $job_id,
            ':JOB_ID2' => $job_id,
            ':JOB_ID3' => $job_id,
        ];

        // unions require each field to be bound to a unique parameter even though they are the same in this case

        $union_sql = 'SELECT
                      PLANOPTION.SEQNO AS OPTION_SEQNO,
                      PLANOPTION.IDCODE AS OPTION_CODE,
                      PLANOPTION.DESCRIPT AS OPTION_NAME,
                      CAST(JOBOPTION.MODIFIEDON AS DATE) AS OPTION_DATE,
                      1 AS OPTION_UNITS,
                      \'\' AS OPTION_LOCATION,
                      \'\' AS OPTION_NOTE
                      FROM JOBOPTION
                      LEFT OUTER JOIN PLANOPTION
                      ON (JOBOPTION.OPTIONID = PLANOPTION.SEQNO)
                      WHERE
                      JOBOPTION.JOBID = :JOB_ID1 AND
                      PLANOPTION.ISELEVATION = 0 AND
                      JOBOPTION.ISREMOVED = 0
                      UNION ALL
                      SELECT
                      PLANOPTION.SEQNO AS OPTION_SEQNO,
                      PLANOPTION.IDCODE AS OPTION_CODE,
                      PLANOPTION.DESCRIPT AS OPTION_NAME,
                      CAST(JOBCHANGEORDERPROJOPTION.MODIFIEDON AS DATE) AS OPTION_DATE,
                      JOBCHANGEORDERPROJOPTION.UNITS AS OPTION_UNITS,
                      PLS_OPTIONS.LOCATION AS OPTION_LOCATION,
                      PLS_OPTIONS.NOTE AS OPTION_NOTE
                      FROM JOBPROJOPTION
                      LEFT OUTER JOIN JOBCHANGEORDERPROJOPTION
                      ON (JOBPROJOPTION.COPROJOPTIONID = JOBCHANGEORDERPROJOPTION.SEQNO)
                      LEFT OUTER JOIN PLANOPTION
                      ON (JOBCHANGEORDERPROJOPTION.OPTIONID = PLANOPTION.SEQNO)
                      LEFT OUTER JOIN JOB
                      ON (JOBPROJOPTION.JOBID = JOB.SEQNO)
                      LEFT OUTER JOIN PLS_OPTIONS
                      ON (JOB.JOBSITEID = PLS_OPTIONS.JOBSITEID) AND
                         (JOB.SOURCEENTITYID = PLS_OPTIONS.SOURCEENTITYID) AND
                         (PLANOPTION.IDCODE LIKE PLS_OPTIONS.OPTIONCODE || \'%\')
                      WHERE
                      JOBPROJOPTION.JOBID = :JOB_ID2 AND
                      JOBPROJOPTION.ISREMOVED = 0
                      UNION ALL 
                      SELECT
                      JOBCHANGEORDER.SEQNO AS OPTION_SEQNO,
                      \'~CO~\' AS OPTION_CODE,
                      JOBCHANGEORDER.DESCRIPT AS OPTION_NAME,
                      JOBCHANGEORDER.ACTIVITYDATE AS OPTION_DATE,
                      1 AS OPTION_UNITS,
                      \'\' AS OPTION_LOCATION,
                      \'\' AS OPTION_NOTE                                            
                      FROM JOBCHANGEORDER
                      WHERE
                      JOBCHANGEORDER.JOBID = :JOB_ID3 AND
                      JOBCHANGEORDER.FINALIZED = 1 AND
                      UPPER(JOBCHANGEORDER.DESCRIPT) NOT LIKE \'%OPTION%\'                      
                      ORDER BY 4 DESC, 3';

        $query = $this->db->select($union_sql, $param_sql);

        return [$query, count($query)];
    }
}
