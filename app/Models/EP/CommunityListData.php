<?php

namespace Models\EP;

use Core\Model;

class CommunityListData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectCommunityListData(array $filters = [], string $limit = ''): array
    {
        $builder_id   = (int) $filters['builder_id'] ?: null;
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $param_sql = [
            ':BUILDER_ID' => $builder_id
        ];

        $select_sql = 'SELECT
                       PROJECT.SEQNO,
                       PROJECT.TABLEID,
                       PROJECT.DESCRIPT,
                       PROJECT.ISPLUMBING,
                       PROJECT.ISCONCRETE,
                       PROJECT.ISFRAMING ';
        $from_sql   = 'FROM PROJECT ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= 'AND PROJECT.OWNERID = :BUILDER_ID ';
        $where_sql  .= 'AND ((COMPLETED IS NULL) OR (COMPLETED = 0)) ';
        $where_sql  .= 'AND ((ISPLUMBING = 1) OR (ISCONCRETE = 1) OR (ISFRAMING = 1)) ';
        $where_sql  .= 'AND (EXISTS(SELECT SEQNO FROM JOB WHERE JOB.PROJECTID = PROJECT.SEQNO)) ';
        $order_sql  = 'ORDER BY PROJECT.DESCRIPT ';

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
