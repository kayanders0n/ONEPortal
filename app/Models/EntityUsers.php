<?php

namespace Models;

use Core\Model;

class EntityUsers extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEntityUsers(array $filters = [], string $limit = ''): array
    {
        $param_sql = [
            ':ENTITY_ID' => (int) $filters['entity_id'] ?: null,
            ':PASSWORD'  => (string) $filters['password'] ?: '',
            ':COMPLETED' => (int) $filters['completed']
        ];

        $select_sql = 'SELECT  
                       ENTITY.SEQNO,
                       ENTITY.TABLEID as ENTITY_NUM,
                       ENTITY.DESCRIPT as ENTITY_NAME,                       
                       ENTITY.EMAIL as ENTITY_EMAIL,
                       ENTITY.PW_HASH as ENTITY_PW_HASH ';
        $from_sql   = 'FROM ENTITY ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= $filters['entity_id'] ? 'AND (ENTITY.TABLEID = :ENTITY_ID) ' : '';
        $where_sql  .= $filters['password'] ? 'AND (ENTITY.PW_HASH = :PASSWORD) ' : '';
        $where_sql  .= isset($filters['completed']) ? 'AND (ENTITY.COMPLETED = :COMPLETED) ' : '';
        $order_sql  = '';
        $limit_sql  = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }
}
