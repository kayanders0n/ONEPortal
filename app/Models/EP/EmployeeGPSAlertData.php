<?php

namespace Models\EP;

use Core\Model;

class EmployeeGPSAlertData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEmployeeGPSAlertData(array $filters = [], string $limit = ''): array
    {
        $item_id = (int)$filters['item_id'] ?: null;
        $employee_num = (int)$filters['employee_num'] ?: null;
        $company_id = (int)$filters['company_id'] ?: null;
        $alert_type = $filters['alert_type'] ?: null;
        $date_start = $filters['date_start'] ?: null;
        $date_end = $filters['date_end'] ?: null;
        $company_site = $filters['company_site'] ?: null;
        $company_department = $filters['company_department'] ?: null;
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $param_sql = [
            ':SEQNO' => $item_id,
            ':EMPLOYEE_NUM' => $employee_num,
            ':ALERT_TYPE' => $alert_type,
            ':DATE_START' => $date_start,
            ':DATE_END' => $date_end,
            ':COMPANY_ID' => $company_id,
            ':COMPANY_SITE' => $company_site,
            ':COMPANY_DEPARTMENT' => $company_department,
        ];

        $select_sql = 'SELECT
                       GPS_EMPLOYEEALERT.SEQNO,
                       GPS_EMPLOYEEALERT.MATERIALASSETID,
                       GPS_EMPLOYEEALERT.EMPLOYEEID,
                       GPS_EMPLOYEEALERT.ACTIVITYDATE,
                       GPS_EMPLOYEEALERT.VEHICLE,
                       GPS_EMPLOYEEALERT.ALERTTIME,
                       GPS_EMPLOYEEALERT.ALERTTYPE,
                       GPS_EMPLOYEEALERT.ALERTNAME,
                       GPS_EMPLOYEEALERT.ALERTCONDITION,
                       GPS_EMPLOYEEALERT.ALERTLATITUDE,
                       GPS_EMPLOYEEALERT.ALERTLONGITUDE,
                       GPS_EMPLOYEEALERT.CREATEDON,
                       EMPLOYEE.TABLEID AS EMPLOYEE_TABLEID,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEEPAYROLL.SALARYGLPREFIX AS EMPLOYEE_GLPREFIX,
                       EMPLOYEEPAYROLL.SALARYGLSUFFIX AS EMPLOYEE_GLSUFFIX,
                       COMPANY.TABLEID AS COMPANY_TABLEID,
                       COMPANY.DESCRIPT AS COMPANY_NAME,
                       MATERIAL.SEQNO AS MATERIAL_SEQNO,
                       MATERIALASSET.IDCODE AS MATERIALASSET_IDCODE,
                       MATERIAL.DESCRIPT AS MATERIAL_NAME ' . PHP_EOL;

        $from_sql = 'FROM GPS_EMPLOYEEALERT
                     LEFT OUTER JOIN EMPLOYEE ON (GPS_EMPLOYEEALERT.EMPLOYEEID = EMPLOYEE.SEQNO)
                     LEFT OUTER JOIN EMPLOYEEPAYROLL ON (GPS_EMPLOYEEALERT.EMPLOYEEID = EMPLOYEEPAYROLL.SEQNO)
                     LEFT OUTER JOIN MATERIALASSET ON (GPS_EMPLOYEEALERT.MATERIALASSETID = MATERIALASSET.SEQNO)
                     LEFT OUTER JOIN MATERIAL ON (MATERIALASSET.MATERIALID = MATERIAL.SEQNO)
                     LEFT OUTER JOIN ENTITY COMPANY ON (EMPLOYEEPAYROLL.SOURCEENTITYID = COMPANY.SEQNO) ' . PHP_EOL;

        $where_sql = 'WHERE 1 = 1 ';
        $where_sql .= $item_id ? 'AND GPS_EMPLOYEEALERT.SEQNO = :SEQNO ' : '';
        $where_sql .= $employee_num ? 'AND EMPLOYEE.TABLEID = :EMPLOYEE_NUM ' : '';
        $where_sql .= $alert_type ? 'AND GPS_EMPLOYEEALERT.ALERTTYPE = :ALERT_TYPE ' : '';
        if ($date_start && $date_end) {
            $where_sql .= 'AND GPS_EMPLOYEEALERT.ACTIVITYDATE BETWEEN :DATE_START AND :DATE_END ' . PHP_EOL;
        }
        $where_sql .= $company_id ? 'AND EMPLOYEEPAYROLL.SOURCEENTITYID = :COMPANY_ID ' : '';
        $where_sql .= $company_site ? 'AND EMPLOYEEPAYROLL.SALARYGLPREFIX = :COMPANY_SITE ' : '';
        $where_sql .= $company_department ? 'AND EMPLOYEEPAYROLL.SALARYGLSUFFIX = :COMPANY_DEPARTMENT ' : '';

        $order_sql = 'ORDER BY  GPS_EMPLOYEEALERT.ALERTTIME ' . PHP_EOL;

        $limit_sql = $limit ?: '';

        if (!$count_only) {
            $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = array();
        }

        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }
}
