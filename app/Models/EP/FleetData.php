<?php

namespace Models\EP;

use Core\Model;

class FleetData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectFleetData(array $filters = [], string $limit = ''): array
    {
        $item_id     = (int) ($filters['item_id'] ?? null);
        $company_id  = (int) ($filters['company_id'] ?? null);

        $param_sql = [
            ':MATERIAL_ID' => $item_id,
            ':COMPANY_ID' => $company_id
        ];

        $select_sql = "SELECT 
                       MATERIAL.SEQNO AS MATERIAL_SEQNO,
                       MATERIAL.IDCODE AS MATERIAL_IDCODE,
                       MATERIAL.DESCRIPT AS MATERIAL_NAME,
                       MATERIAL.USERNUM01 AS MATERIAL_OILCHANGEMILES,
                       MATERIALASSET.SEQNO AS ASSET_SEQNO,
                       MATERIALASSET.IDCODE AS ASSET_IDCODE,
                       MATERIALASSET.SERIALNUM AS ASSET_VIN,
                       MATERIALASSET.LICENSENUM AS ASSET_LICENSE,
                       MATERIALASSET.CONFIGURATIONTYPE AS ASSET_CONFIGURATION,
                       MATERIALASSET.VEHICLEWEIGHT AS ASSET_VEHICLEWEIGHT,
                       MATERIALASSET.SERVDATEIN AS ASSET_SERVICEDATE,
                       MATERIALASSET.REGISTRATIONEXPDATE AS ASSET_REGEXPIRATION,
                       MATERIALASSET.ISEMISSIONSNEEDED AS ASSET_ISEMISSIONSNEEDED,
                       MATERIALASSET.ISADOT AS ASSET_ISADOT,
                       STATUSCODES.DESCRIPT AS ASSET_STATUS,
                       MATERIALASSET.NOTINUSE AS ASSET_PARKED,
                       (SELECT FIRST 1 ODOMETER  FROM MATERIALASSETLOG WHERE (MATERIALASSETID = MATERIALASSET.SEQNO) AND (ODOMETER > 0) AND (UNITS > 0) AND (UPPER(DATATYPE) <> 'OIL CHANGE') ORDER BY ACTIVITYDATE DESC, ODOMETER DESC) AS LAST_ODOMETER,
                       (SELECT FIRST 1 ACTIVITYDATE FROM MATERIALASSETLOG WHERE (MATERIALASSETID = MATERIALASSET.SEQNO) AND (ODOMETER > 0) AND (UNITS > 0) AND (UPPER(DATATYPE) <> 'OIL CHANGE') ORDER BY ACTIVITYDATE DESC, ODOMETER DESC) AS LAST_FUEL_DATE,
                       (SELECT FIRST 1 ODOMETER  FROM MATERIALASSETLOG WHERE (MATERIALASSETID = MATERIALASSET.SEQNO) AND (ODOMETER > 0) AND (UNITS > 0) AND (UPPER(DATATYPE) = 'OIL CHANGE') ORDER BY ACTIVITYDATE DESC, ODOMETER DESC) AS LAST_OILCHANGE,
                       (SELECT FIRST 1 ACTIVITYDATE FROM MATERIALASSETLOG WHERE (MATERIALASSETID = MATERIALASSET.SEQNO) AND (ODOMETER > 0) AND (UNITS > 0) AND (UPPER(DATATYPE) = 'OIL CHANGE') ORDER BY ACTIVITYDATE DESC, ODOMETER DESC) AS LAST_OILCHANGE_DATE,
                       EMPLOYEE.SEQNO AS DRIVER_SEQNO,
                       EMPLOYEE.TABLEID AS DRIVER_TABLEID,
                       EMPLOYEE.DESCRIPT AS DRIVER_NAME,
                       COMPANYSITE.SEQNO AS ASSET_LOCATION_SEQNO,
                       COMPANYSITE.PROFITCENTERNAME AS ASSET_LOCATION,
                       ENTITY.SEQNO AS ASSET_ENTITYID,
                       ENTITY.DESCRIPT AS ASSET_COMPANY,
                       ENTITY.TABLEID AS ASSET_COMPANY_TABLEID,
                       MATERIALASSET.HASGPS AS HAS_GPS,
                       MATERIALASSET.ENGINESIZE AS ENGINE_SIZE " . PHP_EOL;

        $from_sql = 'FROM MATERIALASSET
                     LEFT OUTER JOIN MATERIAL ON (MATERIALASSET.MATERIALID = MATERIAL.SEQNO) 
                     LEFT OUTER JOIN EMPLOYEE ON (MATERIALASSET.ASSIGNEDEMPLOYEEID = EMPLOYEE.SEQNO)
                     LEFT OUTER JOIN COMPANYSITE ON (MATERIALASSET.COSITEID = COMPANYSITE.SEQNO)
                     LEFT OUTER JOIN ENTITY ON (MATERIALASSET.ASSIGNEDSOURCEENTITYID = ENTITY.SEQNO)
                     LEFT OUTER JOIN STATUSCODES ON (MATERIAL.STATUSID = STATUSCODES.SEQNO) ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= "AND (MATERIAL.IDCODE LIKE 'FL%') AND (MATERIAL.IDCODE NOT LIKE 'FLT%') " . PHP_EOL;
        $where_sql .= 'AND MATERIAL.COMPLETED = 0 ' . PHP_EOL;
        $where_sql .= $item_id ? 'AND MATERIAL.SEQNO = :MATERIAL_ID ' : '';
        $where_sql .= $company_id ? 'AND ENTITY.SEQNO = :COMPANY_ID ' : '';
        /**$where_sql .= 'AND ((MATERIALASSET.NOTINUSE = 0) OR (MATERIALASSET.NOTINUSE IS NULL)) ' . PHP_EOL;**/

        $order_sql  = 'ORDER BY 1 DESC ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }




    /**
     * @param $request
     *
     * @return int
     */
/**
    public function updateFleetData($request): int
    {

        $company_id = (int) ($request['company_id'] ?? null);
        $price_increase = (int) ($request['price_increase'] ?? null);

        $price_flag = array();
        switch ($company_id) {
            case PLUMBING_ENTITYID:
                $price_flag = ['USERINT01' => $price_increase];
                break;
            case CONCRETE_ENTITYID:
                $price_flag = ['USERINT02' => $price_increase];
                break;
            case FRAMING_ENTITYID:
                $price_flag = ['USERINT03' => $price_increase];
                break;
        }

        $update = [
            'MODIFIEDBYNAME'     => $request['user_name'],
            'MODIFIEDON'         => date('m/d/Y H:i:s')
        ];

        $update = array_merge($update, $price_flag);

        $where = ['SEQNO' => (int) $request['item_id']];

        return $this->db->update('PROJECT', $update, $where);
    }**/
}

