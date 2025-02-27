<?php

namespace Models\EP;

use Core\Model;

class DocumentData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectDocumentData(array $filters = [], string $limit = ''): array
    {
        $item_id  = (int) $filters['item_id'] ?: null;
        $type_id  = (int) $filters['type_id'] ?: null;
        $data_id  = (int) $filters['data_id'] ?: null;
        $data_ids = (string) $filters['data_ids'] ?: null;
        $data_num = (int) $filters['data_num'] ?: null;


        $param_sql = [
            ':TYPE_ID'    => $type_id,
            ':DATA_ID'    => $data_id,
            ':DATA_NUM'   => $data_num,
        ];

        $select_sql = 'SELECT
                       DOCUMENTSLINK.DATATYPEID AS DATA_TYPE,
                       DOCUMENTSLINK.DATASEQNOID AS DATA_ID,
                       DOCUMENTSLINK.DATATABLEID AS DATA_NUM,
                       DOCUMENTS.SEQNO AS DOCUMENT_SEQNO,
                       DOCUMENTS.DESCRIPT AS DOCUMENT_NAME,
                       DOCUMENTS.DOCUMENTFILE AS DOCUMENT_FILENAME,
                       DOCUMENTS.CREATEDON AS DOCUMENT_MODIFIEDON,
                       DOCUMENTS.SERVERID AS DOCUMENT_SERVERID ';

        $from_sql   = 'FROM DOCUMENTSLINK
                       LEFT OUTER JOIN SECURITYITEM
                       ON (SECURITYITEM.SYSTEMTABLEID = 123) AND /* public access */
                       (SECURITYITEM.DATASEQNO = DOCUMENTSLINK.DOCUMENTID)
                       LEFT OUTER JOIN DOCUMENTS
                       ON (DOCUMENTSLINK.DOCUMENTID = DOCUMENTS.SEQNO) ';

        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $type_id ? 'AND DOCUMENTSLINK.DATATYPEID = :TYPE_ID ' : '';
        $where_sql  .= $data_id ? 'AND DOCUMENTSLINK.DATASEQNOID = :DATA_ID ' : '';
        $where_sql  .= $data_num ? 'AND DOCUMENTSLINK.DATATABLEID = :DATA_NUM ' : '';
        $where_sql  .= $data_ids ? 'AND DOCUMENTSLINK.DATASEQNOID IN (' . $data_ids . ') ' : '';
        $where_sql  .= 'AND SECURITYITEM.GROUPID = -1 ';
        $where_sql  .= "AND ((DOCUMENTS.SERVERID > 0) OR (DOCUMENTS.DOCUMENTFILE LIKE('http%'))) ";


        $order_sql  = 'ORDER BY DOCUMENTS.MODIFIEDON DESC ';

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql , $param_sql);


        return [$query, count($query)];
    }
}
