<?php

namespace Models\EP;

use Core\Model;

class JobTakeoffData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectJobTakeoffData(array $filters = [], string $limit = ''): array
    {
        $item_id = (int) ($filters['item_id'] ?? null);
        $job_id = (int) ($filters['job_id'] ?? null);
        $company_id = (int) ($filters['company_id'] ?? null);

        $param_sql = [
            ':JOB_ID'  => $job_id,
            ':ITEM_ID' => $item_id,
        ];

        $where_sql  = 'WHERE 1=1 ';
        $where_sql .= 'AND MATERIAL.ISLABOR = 0 ';

        // phase, location, adddescription, description because we are going to load it by phase, location to sub arrays

        if ($company_id == PLUMBING_ENTITYID) {
            $select_sql = 'SELECT
                           WORKORDERTAKEOFF.SEQNO AS ITEM_ID,
                           PLANPHASE.PHASEINDEX AS PHASE_INDEX,
                           PLANPHASE.SEQNO AS PHASE_SEQNO,
                           UPPER(WORKORDER.DESCRIPT) AS PHASE_NAME,
                           PLANOPTION.SEQNO AS OPTION_SEQNO,
                           PLANOPTION.IDCODE AS OPTION_IDCODE,
                           PLANOPTION.DESCRIPT AS OPTION_NAME,
                           MATERIAL.SEQNO AS MATERIAL_SEQNO,
                           MATERIAL.IDCODE AS MATERIAL_IDCODE,
                           MATERIAL.ISLABOR AS MATERIAL_LABOR,
                           MATERIAL.DESCRIPT AS MATERIAL_NAME,
                           MATERIALUNITMEASURE.SEQNO AS UOM_SEQNO,
                           MATERIALUNITMEASURE.DESCRIPT AS UOM_NAME,                           
                           WORKORDERTAKEOFF.UNITS AS TAKEOFF_UNITS,
                           CASE WHEN  COALESCE(WORKORDERTAKEOFF.LOCATION, \'\') = \'\' THEN \'000-Unknown\' ELSE WORKORDERTAKEOFF.LOCATION END AS TAKEOFF_LOCATION,
                           WORKORDERTAKEOFF.ADDDESCRIPT AS TAKEOFF_ADDDESCRIPT,
                           (SELECT COUNT(*) FROM DOCUMENTSLINK WHERE DATASEQNOID = MATERIAL.SEQNO) AS DOC_COUNT,
                           WORKORDERTAKEOFF.WAREHOUSESTATUS AS WAREHOUSE_STATUS,
                           WORKORDERTAKEOFF.CREATEDON AS TAKEOFF_CREATEDON ';
            $from_sql = 'FROM WORKORDERTAKEOFF
                         INNER JOIN MATERIAL
                         ON (WORKORDERTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO)
                         LEFT OUTER JOIN WORKORDER
                         ON (WORKORDERTAKEOFF.LINKID = WORKORDER.SEQNO)
                         LEFT OUTER JOIN JOB
                         ON (WORKORDERTAKEOFF.JOBID = JOB.SEQNO)
                         LEFT OUTER JOIN PLANPHASE
                         ON (WORKORDERTAKEOFF.PHASEID = PLANPHASE.SEQNO)
                         LEFT OUTER JOIN PLANOPTION
                         ON (WORKORDERTAKEOFF.OPTIONID = PLANOPTION.SEQNO)                         
                         LEFT OUTER JOIN MATERIALUNITMEASURE
                         ON (MATERIAL.TAKEOFFUM = MATERIALUNITMEASURE.SEQNO) ';
            $where_sql .= $job_id ? 'AND WORKORDERTAKEOFF.JOBID = :JOB_ID ' : '';
            $where_sql .= $item_id ? 'AND WORKORDERTAKEOFF.SEQNO = :ITEM_ID ' : '';

            $order_sql = 'ORDER BY 4,15,16,11 ';

        } else {

            $select_sql = 'SELECT
                           JOBTAKEOFF.SEQNO AS ITEM_ID,
                           PLANPHASE.PHASEINDEX AS PHASE_INDEX,
                           PLANPHASE.SEQNO AS PHASE_SEQNO,
                           UPPER(PLANPHASE.DESCRIPT) AS PHASE_NAME,
                           PLANOPTION.SEQNO AS OPTION_SEQNO,
                           PLANOPTION.IDCODE AS OPTION_IDCODE,
                           PLANOPTION.DESCRIPT AS OPTION_NAME,
                           MATERIAL.SEQNO AS MATERIAL_SEQNO,
                           MATERIAL.IDCODE AS MATERIAL_IDCODE,
                           MATERIAL.ISLABOR AS MATERIAL_LABOR,
                           UPPER(MATERIAL.DESCRIPT) AS MATERIAL_NAME,
                           MATERIALUNITMEASURE.SEQNO AS UOM_SEQNO,
                           MATERIALUNITMEASURE.DESCRIPT AS UOM_NAME,
                           JOBTAKEOFF.UNITS AS TAKEOFF_UNITS,
                           CASE WHEN COALESCE(PLANTAKEOFF.LOCATION, JOBCHANGEORDERTAKEOFF.LOCATION, \'\') = \'\' THEN \'000-Unknown\' ELSE COALESCE(PLANTAKEOFF.LOCATION, JOBCHANGEORDERTAKEOFF.LOCATION, \'\') END AS TAKEOFF_LOCATION,
                           COALESCE(PLANTAKEOFF.ADDDESCRIPT, JOBCHANGEORDERTAKEOFF.ADDDESCRIPT) AS TAKEOFF_ADDDESCRIPT,
                           (SELECT COUNT(*) FROM DOCUMENTSLINK WHERE DATASEQNOID = MATERIAL.SEQNO) AS DOC_COUNT,
                           \'\' AS WAREHOUSE_STATUS,
                           JOBTAKEOFF.CREATEDON AS TAKEOFF_CREATEDON ';
            $from_sql = 'FROM JOBTAKEOFF
                         INNER JOIN MATERIAL
                         ON (JOBTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO)
                         LEFT OUTER JOIN JOB
                         ON (JOBTAKEOFF.JOBID = JOB.SEQNO)
                         LEFT OUTER JOIN PLANPHASE
                         ON (JOBTAKEOFF.PHASEID = PLANPHASE.SEQNO)
                         LEFT OUTER JOIN PLANOPTION
                         ON (JOBTAKEOFF.OPTIONID = PLANOPTION.SEQNO)                         
                         LEFT OUTER JOIN CATEGORYCODES
                         ON (MATERIAL.CATEGORYID = CATEGORYCODES.SEQNO)
                         LEFT OUTER JOIN MATERIALUNITMEASURE
                         ON (MATERIAL.TAKEOFFUM = MATERIALUNITMEASURE.SEQNO)
                         LEFT OUTER JOIN PLANTAKEOFF
                         ON (JOBTAKEOFF.PLANTAKEOFFITEMID = PLANTAKEOFF.SEQNO)
                         LEFT OUTER JOIN JOBCHANGEORDERTAKEOFF
                         ON (JOBTAKEOFF.CHANGEORDERTAKEOFFITEMID = JOBCHANGEORDERTAKEOFF.SEQNO) ';

             $where_sql .= $job_id ? 'AND JOBTAKEOFF.JOBID = :JOB_ID ' : '';
             $where_sql .= $item_id ? 'AND JOBTAKEOFF.SEQNO = :ITEM_ID ' : '';

            $order_sql = 'ORDER BY 2,4,15,16,11 ';
        }

        //error_log($select_sql . $from_sql . $where_sql . $order_sql);
        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql, $param_sql);

        return [$query, count($query)];
    }
}

