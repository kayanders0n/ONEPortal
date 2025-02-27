<?php

namespace Models\EP;

use Core\Model;

class BuilderListData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectBuilderListData(array $filters = [], string $limit = ''): array
    {
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $param_sql = [
        ];

        $select_sql = 'SELECT
                       ENTITY.SEQNO,
                       ENTITY.TABLEID,
                       ENTITY.DESCRIPT ';
        $from_sql   = 'FROM ENTITY ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= 'AND ENTITY.CATEGORYID = 6646 ';
        $where_sql  .= 'AND ENTITY.TYPEID = 55811690 ';
        $where_sql  .= 'AND ENTITY.STATUSID = 6628 ';
        $order_sql  = 'ORDER BY ENTITY.DESCRIPT ';

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
