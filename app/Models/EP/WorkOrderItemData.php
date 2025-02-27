<?php

namespace Models\EP;

use Core\Model;

class WorkOrderItemData extends Model
{
     /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectWorkOrderItemData(array $filters = [], string $limit = ''): array
    {
        $wo_id   = (int) $filters['wo_id'] ?: null;
        $order_view = (int)$filters['order_view'] == 1 ?: false;
        $count_only = (int)$filters['count_only'] == 1 ?: false;


        $param_sql = [
            ':WO_ID'    => $wo_id,
        ];

        $select_sql = 'SELECT  
                       WORKORDERTAKEOFF.SEQNO AS WOITEM_SEQNO, 
                       WORKORDERTAKEOFF.TAKEOFFITEMID AS MATERIAL_SEQNO,
                       MATERIAL.IDCODE AS MATERIAL_IDCODE,
                       MATERIAL.DESCRIPT AS MATERIAL_NAME,
                       MATERIALUNITMEASURE.DESCRIPT AS MATERIAL_UOM,
                       WORKORDERTAKEOFF.UNITS,
                       WORKORDERTAKEOFF.ADDDESCRIPT AS WO_ADDDESCRIPT,
                       WORKORDERTAKEOFF.LOCATION AS WO_LOCATION,
                       WORKORDERTAKEOFF.REFERENCE AS WO_REFERENCE,
                       WORKORDERTAKEOFF.WAREHOUSESTATUS AS WO_WHSTATUS,
                       WORKORDERTAKEOFF.CREATEDON,
                       WORKORDERTAKEOFF.CREATEBYNAME,
                       WORKORDERTAKEOFF.MODIFIEDON,
                       WORKORDERTAKEOFF.MODIFIEDBYNAME ';
        $from_sql   = 'FROM WORKORDERTAKEOFF  
                       LEFT OUTER JOIN MATERIAL 
                       ON (WORKORDERTAKEOFF.TAKEOFFITEMID = MATERIAL.SEQNO) 
                       LEFT OUTER JOIN MATERIALUNITMEASURE
                       ON (MATERIAL.TAKEOFFUM = MATERIALUNITMEASURE.SEQNO)';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $wo_id ? 'AND WORKORDERTAKEOFF.LINKID = :WO_ID ' : '';
        $where_sql  .= $order_view ? "AND ((MATERIAL.ISLABOR = 0) AND (MATERIAL.IDCODE NOT IN('MATERIAL OVERHEAD')) OR (MATERIAL.IDCODE LIKE('GAS %')) OR (MATERIAL.IDCODE IN('SOIL-SINK','T/O-SINK','SOIL-RV DUMP','TUB HOOKUP','SHOWER HOOKUP','DM HOOKUP'))) " : '';

        $order_sql  = 'ORDER BY WORKORDERTAKEOFF.LOCATION, WORKORDERTAKEOFF.ADDDESCRIPT, WORKORDERTAKEOFF.SEQNO ';

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
