<?php

namespace Models\EP;

use Core\Model;

class PurchaseOrderItemData extends Model
{
     /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectPurchaseOrderItemData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) ($filters['item_id'] ?? null);
        $po_id   = (int) ($filters['po_id'] ?? null);
        $count_only = (int)($filters['count_only'] ?? null) == 1 ?: false;


        $param_sql = [
            ':ITEM_ID'  => $item_id,
            ':PO_ID'    => $po_id,
        ];

        $select_sql = 'SELECT  
                       POHEADERITEMS.SEQNO AS POITEM_SEQNO, 
                       POHEADERITEMS.MATERIALID AS MATERIAL_SEQNO,
                       MATERIAL.IDCODE AS MATERIAL_IDCODE,
                       MATERIAL.DESCRIPT AS MATERIAL_NAME,
                       MATERIALUNITMEASURE.DESCRIPT AS MATERIAL_UOM,
                       POHEADERITEMS.ORDERUNITS,
                       POHEADERITEMS.RECEIVEDUNITS,
                       POHEADERITEMS.INVOICEDUNITS,
                       POHEADERITEMS.ADDDESCRIPT AS PO_ADDDESCRIPT,
                       POHEADERITEMS.LOCATION AS PO_LOCATION,
                       POHEADERITEMS.REFERENCE AS PO_REFERENCE,
                       POHEADERITEMS.CREATEDON,
                       POHEADERITEMS.CREATEBYNAME,
                       POHEADERITEMS.MODIFIEDON,
                       POHEADERITEMS.MODIFIEDBYNAME,
                       POHEADERITEMS.JOBID AS JOB_SEQNO,
                       JOB.TABLEID AS JOB_NUM,
                       JOB.IDCODE AS JOB_IDCODE,
                       PROJECT.SEQNO AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       JOBSITE.SEQNO AS JOBSITE_SEQNO,
                       JOBSITE.TABLEID AS JOBSITE_NUM,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       JOBSITE.DESCRIPT AS JOBSITE_NAME,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1 ';
        $from_sql   = 'FROM POHEADERITEMS  
                       LEFT OUTER JOIN MATERIAL 
                       ON (POHEADERITEMS.MATERIALID = MATERIAL.SEQNO) 
                       LEFT OUTER JOIN MATERIALUNITMEASURE
                       ON (MATERIAL.TAKEOFFUM = MATERIALUNITMEASURE.SEQNO) 
                       LEFT OUTER JOIN JOB 
                       ON (POHEADERITEMS.JOBID = JOB.SEQNO) 
                       LEFT OUTER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO) 
                       LEFT OUTER JOIN JOBSITE 
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $item_id ? 'AND POHEADERITEMS.SEQNO = :ITEM_ID ' : '';
        $where_sql  .= $po_id ? 'AND POHEADERITEMS.PURCHORDERID = :PO_ID ' : '';
        $order_sql  = 'ORDER BY POHEADERITEMS.SEQNO ';

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
    public function addPurchaseOrderItemData($request): int
    {

        $values = [
            'po_id'        => (int)$request['po_id'],
            'company_id'   => (int)$request['company_id'],
            'material_id'  => (int)$request['material_id'],
            'order_units'  => (float)$request['order_units'],
            'location'     => strtoupper((string)$request['location']),
            'add_descript' => strtoupper((string)$request['add_descript']),
            'user_name1'   => (string)$request['user_name'],
            'user_name2'   => (string)$request['user_name']
        ];


        $insert = 'INSERT INTO POHEADERITEMS 
                   (SEQNO, PURCHORDERID, SOURCEENTITYID, 
                   MATERIALID, ORDERUNITS, LOCATION, ADDDESCRIPT, 
                   CREATEBYNAME, CREATEDON, MODIFIEDBYNAME, MODIFIEDON) 
                   VALUES(GEN_ID(SYSTEMSEQNONUMBER, 1), :po_id, :company_id, 
                   :material_id, :order_units, :location, :add_descript, 
                   :user_name1, CURRENT_TIMESTAMP, :user_name2, CURRENT_TIMESTAMP) RETURNING SEQNO';

        $stmt = $this->db->prepare($insert);

        foreach ($values as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $result = $stmt->fetchAll();
        return (int) $result[0]['SEQNO'];
    }


    /**
     * @param $request
     *
     * @return int
     */
    public function updatePurchaseOrderItemData($request): int
    {

        $update = [
            'ORDERUNITS'        => (float)$request['order_units'],
            'LOCATION'           => strtoupper(html_entity_decode($request['location'], ENT_QUOTES)),
            'ADDDESCRIPT'        => strtoupper(html_entity_decode($request['add_descript'], ENT_QUOTES)),
            'MODIFIEDBYNAME'     => $request['user_name'],
            'MODIFIEDON'         => date('m/d/Y H:i:s')
        ];

        $where = ['SEQNO' => (int) $request['item_id']];

        return $this->db->update('POHEADERITEMS', $update, $where);
    }


    /**
     * @param $request
     *
     * @return int
     */
    public function removePurchaseOrderItemData($request): int
    {

        $delete = [
            'SEQNO' => (int)$request['item_id'],
        ];

        return $this->db->delete('POHEADERITEMS', $delete);
    }
}
