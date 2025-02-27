<?php

namespace Models\EP;

use Core\Model;
use Helpers\DatabaseNoteUpdate;

class PurchaseOrderData extends Model
{
    /**
     * @param int $po_num
     * @return int
     */
    function getPurchaseOrderSeqnoFromNum(int $po_num): int
    {
        $param_sql = [
            ':PO_NUM' => $po_num,
        ];

        $select_sql = 'SELECT SEQNO FROM POHEADER WHERE TABLEID = :PO_NUM';

        $query = $this->db->selectOne($select_sql, $param_sql);

        return (int)$query->SEQNO;
    }

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectPurchaseOrderData(array $filters = [], string $limit = ''): array
    {
        $item_id   = (int) ($filters['item_id'] ?? null);
        $po_num   = (int) ($filters['po_num'] ?? null);
        $company_id = (int) ($filters['company_id'] ?? null);
        $po_type = (int) ($filters['type'] ?? null);
        $po_date = (string) ($filters['date'] ?? null);
        $reference = (string) ($filters['reference'] ?? null);
        $completed = (int) ($filters['completed'] ?? -1);
        $count_only = (int)(($filters['count_only'] ?? false) == 1);


        $param_sql = [
            ':PO_ID'    => $item_id,
            ':PO_NUM'   => $po_num,
            ':COMPANY_ID' => $company_id,
            ':PO_TYPE' => $po_type,
            ':PO_DATE' => $po_date,
            ':REFERENCE' => $reference,
            ':COMPLETED' => $completed,
        ];

        $select_sql = 'SELECT  
                       POHEADER.SEQNO AS PO_SEQNO, 
                       POHEADER.TABLEID AS PO_NUM,
                       POHEADER.DESCRIPT AS PO_NAME,
                       POHEADER.PODATE,
                       POHEADER.DATATYPEID AS PO_DATATYPEID,
                       POHEADERTYPE.DESCRIPT AS PO_TYPE,
                       POHEADER.STATUSID AS PO_STATUSID,
                       STATUSCODES.DESCRIPT AS STATUS_NAME,
                       POHEADER.COMPLETEDON AS PO_COMPLETEDON,
                       POHEADER.SOURCEENTITYID AS SOURCEENTITY_SEQNO,
                       COMPANY.TABLEID AS SOURCEENTITY_NUM,
                       COMPANY.DESCRIPT AS SOURCEENTITY_NAME,                      
                       POHEADER.ENTITYID AS VENDOR_SEQNO,
                       VENDOR.TABLEID AS VENDOR_NUM,
                       VENDOR.DESCRIPT AS VENDOR_NAME,
                       POHEADER.REFERENCE AS PO_REFERENCE,
                       POHEADER.COMMENT AS PO_COMMENT,
                       POHEADER.JOBID AS JOB_SEQNO,
                       JOB.TABLEID AS JOB_NUM,
                       JOB.JOBSITEID AS JOBSITE_SEQNO,
                       JOBSITE.TABLEID AS JOBSITE_NUM,
                       JOBSITE.IDCODE AS JOBSITE_IDCODE,
                       JOBSITE.DESCRIPT AS JOBSITE_NAME,
                       JOBSITE.ADDRESS1 AS JOBSITE_ADDRESS1,
                       JOBSITE.ADDRESS2 AS JOBSITE_ADDRESS2,
                       JOBSITE.CITY AS JOBSITE_CITY,
                       JOBSITE.STATE AS JOBSITE_STATE,
                       JOBSITE.ZIP AS JOBSITE_ZIP,
                       JOB.ENTITYID AS BUILDER_SEQNO,
                       BUILDER.TABLEID AS BUILDER_NUM,
                       BUILDER.DESCRIPT AS BUILDER_NAME,
                       JOB.PROJECTID AS PROJECT_SEQNO,
                       PROJECT.TABLEID AS PROJECT_NUM,
                       PROJECT.DESCRIPT AS PROJECT_NAME,
                       POHEADER.SHIPLOCATIONID AS SHIP_SEQNO,
                       SHIPADDRESS.TABLEID AS SHIP_NUM,
                       SHIPADDRESS.DESCRIPT AS SHIP_NAME,
                       POHEADER.NOTE AS PO_NOTE, 
                       POHEADER.CREATEDON,
                       POHEADER.CREATEBYNAME,
                       POHEADER.MODIFIEDON,
                       POHEADER.MODIFIEDBYNAME ';
        $from_sql   = 'FROM POHEADER  
                       LEFT OUTER JOIN ENTITY COMPANY 
                       ON (POHEADER.SOURCEENTITYID = COMPANY.SEQNO) 
                       LEFT OUTER JOIN ENTITY VENDOR 
                       ON (POHEADER.ENTITYID = VENDOR.SEQNO)
                       LEFT OUTER JOIN POHEADERTYPE
                       ON (POHEADER.DATATYPEID = POHEADERTYPE.SEQNO)
                       LEFT OUTER JOIN STATUSCODES 
                       ON (POHEADER.STATUSID = STATUSCODES.SEQNO)
                       LEFT OUTER JOIN JOB
                       ON (POHEADER.JOBID = JOB.SEQNO) 
                       LEFT OUTER JOIN JOBSITE
                       ON (JOB.JOBSITEID = JOBSITE.SEQNO)
                       LEFT OUTER JOIN PROJECT
                       ON (JOB.PROJECTID = PROJECT.SEQNO)
                       LEFT OUTER JOIN ENTITY BUILDER 
                       ON (JOB.ENTITYID = BUILDER.SEQNO) 
                       LEFT OUTER JOIN ENTITYADDRESS SHIPADDRESS
                       ON (POHEADER.SHIPLOCATIONID = SHIPADDRESS.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $item_id ? 'AND POHEADER.SEQNO = :PO_ID ' : '';
        $where_sql  .= $po_num ? 'AND POHEADER.TABLEID = :PO_NUM ' : '';
        $where_sql  .= $company_id ? 'AND POHEADER.SOURCEENTITYID = :COMPANY_ID ' : '';
        $where_sql  .= $po_type ? 'AND POHEADER.DATATYPEID = :PO_TYPE ' : '';
        $where_sql  .= $po_date ? 'AND POHEADER.PODATE = :PO_DATE ' : '';
        $where_sql  .= $reference ? 'AND POHEADER.REFERENCE = :REFERENCE ' : '';
        $where_sql  .= $completed != -1 ? 'AND POHEADER.COMPLETED = :COMPLETED ' : '';
        $order_sql  = 'ORDER BY POHEADER.TABLEID ';

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
    public function updatePurchaseOrderData($request): int
    {
        $where = ['SEQNO' => (int) $request['item_id']];

        if (!empty($request['note'])) {
            $update = [
                'note_field' => 'NOTE',
                'note'       => html_entity_decode($request['note'], ENT_QUOTES),
                'user_name'  => $request['user_name'],
                'update' => [
                    'MODIFIEDBYNAME'     => $request['user_name'],
                    'MODIFIEDON'         => date('m/d/Y H:i:s')
                ]
            ];

            return DatabaseNoteUpdate::update($this->db, 'POHEADER', $update, $where);

        } else {
            $update = [
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        return $this->db->update('POHEADER', $update, $where);
    }


    /**
     * @param $request
     *
     * @return int
     */
    public function printPurchaseOrderData($request): int
    {

        $values = [
            'po_id' => (int)$request['item_id'],
            'po_num' => (int)$request['po_num'],
            'company_id' => (int)$request['company_id'],
            'employee_id' => (int)$request['employee_id'],
            'site_code' => (string)$request['site_code'],
            'override_print' => (string)$request['override_print'],
            'user_name' => (string)$request['user_name']
        ];


        $insert = 'INSERT INTO POHEADER_PRINTQUEUE 
                   (SEQNO, PURCHORDERID, PURCHORDERTABLEID, SOURCEENTITYID, SOURCEEMPLOYEEID, SITECODE, OVERRIDE_PRINT, 
                   PRINTED, PRINT_COUNT, CREATEBYNAME, CREATEDON) 
                   VALUES(GEN_ID(SYSTEMSEQNONUMBER, 1), :po_id, :po_num, :company_id, :employee_id, :site_code, :override_print, 
                   0, 1, :user_name, CURRENT_TIMESTAMP) RETURNING SEQNO';

        $stmt = $this->db->prepare($insert);

        foreach ($values as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $result = $stmt->fetchAll();
        return (int) $result[0]['SEQNO'];

    }
}
