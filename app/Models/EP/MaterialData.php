<?php

namespace Models\EP;

use Core\Model;
use Helpers\Numeric;

class MaterialData extends Model
{


    /**
     * @param int $material_num
     * @return array
     */
    function getMaterialFromNum(int $material_num): array
    {
        $param_sql = [
            ':MATERIAL_NUM' => $material_num,
        ];

        $select_sql = 'SELECT SEQNO, TABLEID, IDCODE, DESCRIPT FROM MATERIAL WHERE TABLEID = :MATERIAL_NUM';

        $query = $this->db->selectOne($select_sql, $param_sql);

        return ['id' => (int) ($query->SEQNO ?? null), 'num' => (int) ($query->TABLEID ?? null), 'code' => (string) ($query->IDCODE ?? null), 'name' => (string) ($query->DESCRIPT ?? null)];
    }

    /**
     * @param array $filters
     * @param string $limit
     * @return array
     */
    public function selectMaterialData(array $filters = [], string $limit = ''): array
    {

        $item_id         = (int)($filters['item_id'] ?? null);
        $category_id     = (int)($filters['category_id'] ?? null);
        $material_idcode = (string)($filters['code'] ?? null);
        $material_upc    = (string)($filters['upc'] ?? null);
        $product_search  = (string)cleanString(strtoupper($filters['product_search'] ?? null));
        $count_only      = (int)(($filters['count_only'] ?? false) == 1);

        // not searching by UPC directly but the product search looks like a UPC so use it
        if (Numeric::isUPC($product_search) && (trim($material_upc) == '')) {
            // if it is a valid upc then search that way
            $material_upc = $product_search;
            $product_search = '';
        }

        $param_sql = [
            ':ITEM_ID'         => $item_id,
            ':CATEGORY_ID'     => $category_id,
            ':MATERIAL_IDCODE' => strtoupper($material_idcode),
            ':MATERIAL_UPC1'   => $material_upc,
            ':MATERIAL_UPC2'   => $material_upc,
            ':PRODUCT_SEARCH1' => '%' . substr($product_search, 0, 18) . '%',
            ':PRODUCT_SEARCH2' => '%' . substr($product_search, 0, 78) . '%',
        ];

        $select_sql = 'SELECT
                       MATERIAL.SEQNO AS MATERIAL_SEQNO,
                       MATERIAL.TABLEID AS MATERIAL_TABLEID,
                       MATERIAL.IDCODE AS MATERIAL_IDCODE, 
                       MATERIAL.DESCRIPT AS MATERIAL_NAME,
                       MATERIAL.UPC AS MATERIAL_UPC, 
                       CATEGORYCODES.SEQNO AS CATEGORY_SEQNO,
                       CATEGORYCODES.DESCRIPT AS CATEGORY_NAME,
                       TAKEOFFUOM.DESCRIPT AS TAKEOFF_UOM ';

        $from_sql   = 'FROM MATERIAL 
                       LEFT OUTER JOIN CATEGORYCODES 
                       ON (MATERIAL.CATEGORYID = CATEGORYCODES.SEQNO) 
                       LEFT OUTER JOIN MATERIALUNITMEASURE TAKEOFFUOM 
                       ON (MATERIAL.TAKEOFFUM = TAKEOFFUOM.SEQNO) ';

        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql .= 'AND MATERIAL.COMPLETED = 0 ';
        $where_sql .= $item_id ? 'AND MATERIAL.SEQNO = :ITEM_ID ' : '';
        if (!$item_id) {
            $where_sql .= $category_id ? 'AND MATERIAL.CATEGORYID = :CATEGORY_ID ' : '';
            $where_sql .= $material_idcode ? 'AND MATERIAL.IDCODE = :MATERIAL_IDCODE ' : '';
            $where_sql .= $product_search ? 'AND ((MATERIAL.IDCODE LIKE :PRODUCT_SEARCH1) OR (UPPER(MATERIAL.DESCRIPT) LIKE :PRODUCT_SEARCH2)) ' : '';
            $where_sql .= $material_upc ? 'AND ((MATERIAL.UPC = :MATERIAL_UPC1) OR (EXISTS(SELECT SEQNO FROM MATERIALMANUFACTURERS WHERE MATERIALID = MATERIAL.SEQNO AND UPC = :MATERIAL_UPC2))) ' : '';
        }

        $order_sql  = 'ORDER BY MATERIAL.DESCRIPT ';

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
     * @param int $item_id
     * @return object
     */
    public function getMaterialItemPriceData(int $item_id): array
    {

        $param_sql = [
            ':ITEM_ID'  => $item_id,
        ];

        $select_sql = 'SELECT 
                       MATERIALSUPPLIERS.SEQNO AS PRICE_SEQNO,
                       ENTITY.DESCRIPT AS SUPPLIER_NAME,
                       COMPANYSITE.DESCRIPT AS SITE_NAME,
                       MATERIALSUPPLIERS.PRICE AS SUPPLIER_PRICE,
                       MATERIALSUPPLIERS.REFERENCE AS SUPPLIER_REFERENCE ';
        $from_sql   = 'FROM MATERIALSUPPLIERS
                       LEFT OUTER JOIN COMPANYSITE
                       ON (MATERIALSUPPLIERS.COSITEID = COMPANYSITE.SEQNO)
                       LEFT OUTER JOIN ENTITY
                       ON (MATERIALSUPPLIERS.ENTITYID = ENTITY.SEQNO) ';
        $where_sql  = 'WHERE 1=1 ';
        $where_sql .= 'AND CURRENT_DATE BETWEEN MATERIALSUPPLIERS.EFFECTIVEDATE AND MATERIALSUPPLIERS.EXPIREDATE ';
        $where_sql .= 'AND MATERIALSUPPLIERS.PRICE > 0 ';
        $where_sql .= 'AND MATERIALSUPPLIERS.MATERIALID = :ITEM_ID ';

        $order_sql = 'ORDER BY COMPANYSITE.TABLEID, ENTITY.DESCRIPT';

        if ($query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql, $param_sql)) {
            return $query;
        } else {
            return [];
        }
    }


    /**
     * @param int $item_id
     * @return object
     */
    public function getMaterialItemUPCData(int $item_id): array
    {

        $param_sql = [
            ':ITEM_ID'  => $item_id,
        ];

        $select_sql = 'SELECT
                       MATERIALMANUFACTURERS.SEQNO AS UPC_SEQNO,
                       MATERIALMANUFACTURERS.UPC ';
        $from_sql   = 'FROM MATERIALMANUFACTURERS ';
        $where_sql  = 'WHERE 1=1 ';
        $where_sql .= 'AND MATERIALMANUFACTURERS.MATERIALID = :ITEM_ID ';

        $order_sql = 'ORDER BY MATERIALMANUFACTURERS.SEQNO';

        if ($query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql, $param_sql)) {
            return $query;
        } else {
            return [];
        }
    }


    /**
     * @param array $filters
     * @param string $limit
     * @return array
     */
    public function selectMaterialCategoryData(array $filters = [], string $limit = ''): array
    {

        $param_sql = [
        ];

        $select_sql = 'SELECT
                       CATEGORYCODES.SEQNO AS ITEM_ID,
                       CATEGORYCODES.DESCRIPT AS CATEGORY_NAME ';

        $from_sql   = 'FROM CATEGORYCODES ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql .= 'AND CATEGORYCODES.MATERIAL = 1 ';
        $order_sql  = 'ORDER BY CATEGORYCODES.DESCRIPT ';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql, $param_sql);

        return [$query, count($query)];
    }



    /**
     * @param $request
     *
     * @return int
     */
    public function updateMaterialData($request): int
    {
        $where = ['SEQNO' => (int) $request['item_id']];

        if (!empty($request['upc'])) {

            $add_upc = strtoupper((string) ($request['upc'] ?? null));
            $default_upc = strtoupper((string) ($request['default_upc'] ?? null));

            if ($add_upc == $default_upc) { return -1; }

            if (trim($default_upc) == '') {

                $update = [
                    'UPC' => $add_upc,
                    'MODIFIEDBYNAME' => $request['user_name'],
                    'MODIFIEDON' => date('m/d/Y H:i:s')
                ];

            } else {

                $values = [
                    'material_id' => (int)$request['item_id'],
                    'upc' => $add_upc,
                    'user_name1' => (string)$request['user_name'],
                    'user_name2' => (string)$request['user_name']
                ];

                $insert = 'INSERT INTO MATERIALMANUFACTURERS 
                   (SEQNO, MATERIALID, UPC, 
                    CREATEBYNAME, CREATEDON, MODIFIEDBYNAME, MODIFIEDON) 
                   VALUES(GEN_ID(SYSTEMSEQNONUMBER, 1), :material_id, :upc, 
                   :user_name1, CURRENT_TIMESTAMP, :user_name2, CURRENT_TIMESTAMP) RETURNING SEQNO';

                $stmt = $this->db->prepare($insert);

                foreach ($values as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }

                $stmt->execute();

                $result = $stmt->fetchAll();
                return (int) $result[0]['SEQNO'];

            }

        } else {
            $update = [
                'MODIFIEDBYNAME'     => $request['user_name'],
                'MODIFIEDON'         => date('m/d/Y H:i:s')
            ];
        }

        return $this->db->update('MATERIAL', $update, $where);
    }
}