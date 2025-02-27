<?php

namespace Models\EP;

use Core\Model;

class JobLaborData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobLaborData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;
        $company_id = (int) $filters['company_id'] ?: 0;
        $flex_only = (int) $filters['flex_only'] ?: 0;

        $param_sql = [
            ':JOB_ID' => $job_id,
        ];

        $where_sql  = 'WHERE 1=1 ';
        $where_sql .= 'AND JOBTAKEOFF.JOBID = :JOB_ID ';
        $where_sql .= 'AND MATERIAL.ISLABOR = 1 ';
        $where_sql .= 'AND MATERIAL.ISOHLABOR = 0 ';
        $where_sql .= $flex_only ? 'AND MATERIAL.REFERENCE = \'FLEX\'' : '';

        $group_sql = 'GROUP BY 1,2,3 ';
        $order_sql = 'ORDER BY 1,2 ';

        if ($company_id == PLUMBING_ENTITYID) {
            $select_sql = 'SELECT
                           PLANPHASE.PHASEINDEX AS PHASE_INDEX,
                           UPPER(CASE PLANPHASE.DESCRIPT WHEN \'Warranty\' THEN \'Gas\' ELSE PLANPHASE.DESCRIPT END) AS PHASE_NAME,
                           \'USD\' AS BUDGET_UOM,
                           SUM(JOBTAKEOFF.TOTALCOST) AS SUM_TOTAL ';

            $from_sql = 'FROM JOBTAKEOFF
                         INNER JOIN MATERIAL
                         ON (JOBTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO)
                         INNER JOIN PLANPHASE
                         ON (JOBTAKEOFF.PHASEID = PLANPHASE.SEQNO) ';

            $where_sql .= 'AND ((PLANPHASE.DESCRIPT NOT IN (\'Warranty\')) OR (MATERIAL.IDCODE IN (\'FINAL GAS\'))) ';

        } else if ($company_id == CONCRETE_ENTITYID) {

            $select_sql = 'SELECT
                           \'999.000\' AS PHASE_INDEX, 
                           UPPER(MATERIAL.IDCODE || \'- \' || MATERIAL.DESCRIPT) AS PHASE_NAME,
                           \'MH\' AS BUDGET_UOM,
                           SUM(JOBTAKEOFF.UNITS) AS SUM_TOTAL ';

            $from_sql = 'FROM JOBTAKEOFF
                         INNER JOIN MATERIAL
                         ON (JOBTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO) ';


            $where_sql .= 'AND (JOBTAKEOFF.TAKEOFFITEMID IN (9534362,9536968,9537896,9534550,9583061,9534560,9536747,125126128,125130304,125130317)) ';

            // C456H, C446, C466, C465, C420, C448, C421, C152, CFP400H, CFP300H

        } else if ($company_id == FRAMING_ENTITYID) {
            $select_sql = 'SELECT
                           \'999.000\' AS PHASE_INDEX, 
                           \'FRAMING\' AS PHASE_NAME,
                           \'USD\' AS BUDGET_UOM,
                           SUM(JOBTAKEOFF.TOTALCOST) AS SUM_TOTAL ';

            $from_sql = 'FROM JOBTAKEOFF
                         INNER JOIN MATERIAL
                         ON (JOBTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO) ';

        } else {

            return [array(), 0];

        }

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql, $param_sql);

        return [$query, count($query)];
    }


    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobLaborOtherData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;
        $company_id = (int) $filters['company_id'] ?: 0;
        $non_flex = (int) $filters['non_flex'] ?: 0;

        $param_sql = [
            ':JOB_ID' => $job_id,
        ];

        $where_sql  = 'WHERE 1=1 ';
        $where_sql .= 'AND JOBTAKEOFF.JOBID = :JOB_ID ';
        $where_sql .= 'AND MATERIAL.ISLABOR = 1 ';
        $where_sql .= 'AND MATERIAL.ISOHLABOR = 0 ';
        $where_sql .= $non_flex ? 'AND ((MATERIAL.REFERENCE <> \'FLEX\') OR (MATERIAL.REFERENCE IS NULL)) ' : '';

        $group_sql = 'GROUP BY 1,2,3,4,5 ';
        $order_sql = 'ORDER BY 1,2 ';

        if ($company_id == PLUMBING_ENTITYID) {
            $select_sql = 'SELECT
                           PLANPHASE.PHASEINDEX AS PHASE_INDEX,
                           UPPER(CASE PLANPHASE.DESCRIPT WHEN \'Warranty\' THEN \'Gas\' ELSE PLANPHASE.DESCRIPT END) AS PHASE_NAME,
                           MATERIAL.IDCODE AS MATERIAL_IDCODE,
                           MATERIAL.DESCRIPT AS MATERIAL_NAME,
                           \'USD\' AS BUDGET_UOM,
                           SUM(JOBTAKEOFF.TOTALCOST) AS SUM_TOTAL ';

            $from_sql = 'FROM JOBTAKEOFF
                         INNER JOIN MATERIAL
                         ON (JOBTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO)
                         INNER JOIN PLANPHASE
                         ON (JOBTAKEOFF.PHASEID = PLANPHASE.SEQNO) ';

            $where_sql .= 'AND ((PLANPHASE.DESCRIPT NOT IN (\'Warranty\')) OR (MATERIAL.IDCODE IN (\'FINAL GAS\'))) ';

        } else {

            return [array(), 0];

        }

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $group_sql . $order_sql, $param_sql);

        return [$query, count($query)];
    }


    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobLaborActualData(array $filters = [], string $limit = ''): array
    {
        $job_id = (int) $filters['job_id'] ?: 0;
        $company_id = (int) $filters['company_id'] ?: 0;

        $param_sql = [
            ':JOB_ID1' => $job_id,
            ':JOB_ID2' => $job_id,
            ':JOB_ID3' => $job_id,
            ':JOB_ID4' => $job_id,
        ];


        if ($company_id == PLUMBING_ENTITYID) {
            $union_sql = 'SELECT
                          UPPER(PLANPHASE.DESCRIPT) AS PHASE_NAME,
                          SUM(PRPAYMENTITEMS.TOTALAMT) AS ACTUAL_AMT
                          FROM PRPAYMENTITEMS
                          LEFT OUTER JOIN EMPLOYEE
                          ON (PRPAYMENTITEMS.EMPLOYEEID = EMPLOYEE.SEQNO)
                          LEFT OUTER JOIN JOBPHASE
                          ON (PRPAYMENTITEMS.JOBPHASEID = JOBPHASE.SEQNO)
                          LEFT OUTER JOIN PLANPHASE
                          ON (JOBPHASE.PLANPHASEID = PLANPHASE.SEQNO)
                          LEFT OUTER JOIN TASKS
                          ON (PRPAYMENTITEMS.TASKID = TASKS.SEQNO)
                          WHERE
                          PRPAYMENTITEMS.JOBID = :JOB_ID1 AND
                          PRPAYMENTITEMS.TAXCLASSID <> 13 AND
                          PRPAYMENTITEMS.VOIDITEMID = 0
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          UPPER(PLANPHASE.DESCRIPT) AS PHASE_NAME,
                          SUM(PRINPUT.TOTALAMT) AS ACTUAL_AMT
                          FROM PRINPUT                          
                          LEFT OUTER JOIN JOBPHASE
                          ON (PRINPUT.JOBPHASEID = JOBPHASE.SEQNO)
                          LEFT OUTER JOIN PLANPHASE
                          ON (JOBPHASE.PLANPHASEID = PLANPHASE.SEQNO)
                          LEFT OUTER JOIN TASKS
                          ON (PRINPUT.TASKID = TASKS.SEQNO)
                          WHERE
                          PRINPUT.JOBID = :JOB_ID2 AND
                          PRINPUT.TAXCLASSID <> 13
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          \'SUB LABOR\' AS PHASE_NAME,
                          SUM(POHEADERITEMS.ORDERUNITS) AS ACTUAL_AMT
                          FROM POHEADER
                          INNER JOIN POHEADERITEMS
                          ON (POHEADER.SEQNO = POHEADERITEMS.PURCHORDERID)
                          WHERE
                          POHEADER.JOBID = :JOB_ID3 AND
                          POHEADERITEMS.ACCOUNTID = 16038 /* SUBCONTRACTOR GL ACCOUNT# */ AND
                          ((POHEADER.COMPLETED = 0) OR (POHEADER.COMPLETED IS NULL))
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          \'SUB LABOR\' AS PHASE_NAME,
                          SUM(APINVOICEITEMS.TOTALAMT) AS ACTUAL_AMT
                          FROM APINVOICEITEMS
                          WHERE
                          APINVOICEITEMS.JOBID = :JOB_ID4 AND
                          APINVOICEITEMS.ACCOUNTID = 16038 /* SUBCONTRACTOR GL ACCOUNT# */
                          GROUP BY 1
                          ORDER BY 1';

        } else if ($company_id == FRAMING_ENTITYID) {
            $union_sql = 'SELECT
                          \'FRAMING\' AS PHASE_NAME,
                          SUM(PRPAYMENTITEMS.TOTALAMT) AS ACTUAL_AMT
                          FROM PRPAYMENTITEMS
                          LEFT OUTER JOIN EMPLOYEE
                          ON (PRPAYMENTITEMS.EMPLOYEEID = EMPLOYEE.SEQNO)
                          LEFT OUTER JOIN JOBPHASE
                          ON (PRPAYMENTITEMS.JOBPHASEID = JOBPHASE.SEQNO)
                          LEFT OUTER JOIN PLANPHASE
                          ON (JOBPHASE.PLANPHASEID = PLANPHASE.SEQNO)
                          LEFT OUTER JOIN TASKS
                          ON (PRPAYMENTITEMS.TASKID = TASKS.SEQNO)
                          WHERE
                          PRPAYMENTITEMS.JOBID = :JOB_ID1 AND
                          PRPAYMENTITEMS.TAXCLASSID <> 13 AND
                          PRPAYMENTITEMS.VOIDITEMID = 0
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          \'FRAMING\' AS PHASE_NAME,
                          SUM(PRINPUT.TOTALAMT) AS ACTUAL_AMT
                          FROM PRINPUT                          
                          LEFT OUTER JOIN JOBPHASE
                          ON (PRINPUT.JOBPHASEID = JOBPHASE.SEQNO)
                          LEFT OUTER JOIN PLANPHASE
                          ON (JOBPHASE.PLANPHASEID = PLANPHASE.SEQNO)
                          LEFT OUTER JOIN TASKS
                          ON (PRINPUT.TASKID = TASKS.SEQNO)
                          WHERE
                          PRINPUT.JOBID = :JOB_ID2 AND
                          PRINPUT.TAXCLASSID <> 13
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          \'SUB LABOR\' AS PHASE_NAME,
                          SUM(POHEADERITEMS.ORDERUNITS) AS ACTUAL_AMT
                          FROM POHEADER
                          INNER JOIN POHEADERITEMS
                          ON (POHEADER.SEQNO = POHEADERITEMS.PURCHORDERID)
                          WHERE
                          POHEADER.JOBID = :JOB_ID3 AND
                          POHEADERITEMS.ACCOUNTID = 16038 /* SUBCONTRACTOR GL ACCOUNT# */ AND
                          ((POHEADER.COMPLETED = 0) OR (POHEADER.COMPLETED IS NULL))
                          GROUP BY 1
                          UNION ALL
                          SELECT
                          \'SUB LABOR\' AS PHASE_NAME,
                          SUM(APINVOICEITEMS.TOTALAMT) AS ACTUAL_AMT
                          FROM APINVOICEITEMS
                          WHERE
                          APINVOICEITEMS.JOBID = :JOB_ID4 AND
                          APINVOICEITEMS.ACCOUNTID = 16038 /* SUBCONTRACTOR GL ACCOUNT# */
                          GROUP BY 1
                          ORDER BY 1';
        } else {

            return [array(), 0];

        }

        $query = $this->db->select($union_sql, $param_sql);

        return [$query, count($query)];
    }

}

