<?php

namespace Models\EP;

use Core\Model;

class LotListData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectLotListData(array $filters = [], string $limit = ''): array
    {
        $community_id = (int) $filters['community_id'] ?: null;
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $param_sql = [
            ':COMMUNITY_ID' => $community_id,
        ];

        $select_sql = 'SELECT
                      JOB.SEQNO,
                      JOB.TABLEID,
                      JOBSITE.IDCODE,
                      JOBSITE.DESCRIPT,
                      JOB.SOURCEENTITYID,
                      JOB.STARTDATE ';
        $from_sql   = 'FROM JOB
                      LEFT OUTER JOIN JOBSITE ON (JOB.JOBSITEID = JOBSITE.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= 'AND JOB.PROJECTID = :COMMUNITY_ID ';
        $order_sql  = 'ORDER BY JOB.TABLEID ';

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
