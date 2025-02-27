<?php

namespace Models\EP;

use Core\Model;

class PLSJobsiteData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectPLSJobsiteData(array $filters = [], string $limit = ''): array
    {
        $expired_publish = (int)$filters['expired_publish'] ?: null;
        $count_only = (int)$filters['count_only'] == 1 ?: false;
        $item_id = (int)$filters['item_id'] ?: null;

        $param_sql = [
            ':SEQNO' => $item_id,
        ];

        $select_sql = 'SELECT
                       PLS_JOBSITE.SEQNO, 
                       PLS_JOBSITE.ACTIVITYDATE,                                                                     
                       PLS_JOBSITE.ISPLUMBING,
                       PLS_JOBSITE.ISCONCRETE,
                       PLS_JOBSITE.ISFRAMING, 
                       PLS_JOBSITE.CREATEDON,
                       PLS_JOBSITE.JOBSITEID AS JOBSITE_SEQNO,        
                       PLS_JOBSITE.TABLEID AS JOBSITE_TABLEID ';

        $from_sql = 'FROM PLS_JOBSITE ';
        $where_sql = 'WHERE 1 = 1 ';

        if ($expired_publish) {
            $where_sql .= 'AND PLS_JOBSITE.PUBLISHED = 0 ';
            $where_sql .= 'AND (EXISTS(SELECT PLS_JOBSITE_REVIEW.SEQNO FROM PLS_JOBSITE_REVIEW WHERE PLS_JOBSITE_REVIEW.PLS_JOBSITEID = PLS_JOBSITE.SEQNO AND PLS_JOBSITE_REVIEW.PROCESSED = 1 AND PLS_JOBSITE_REVIEW.PROCESSEDON < DATEADD(-4 HOUR TO CURRENT_TIMESTAMP))) ';
            $where_sql .= 'AND (NOT EXISTS(SELECT PLS_JOBSITE_REVIEW.SEQNO FROM PLS_JOBSITE_REVIEW WHERE PLS_JOBSITE_REVIEW.PLS_JOBSITEID = PLS_JOBSITE.SEQNO AND PLS_JOBSITE_REVIEW.PROCESSED = 0)) ';
        } else {
            $where_sql .= $item_id ? 'AND PLS_JOBSITE.SEQNO = :SEQNO ' : '';
        }

        $order_sql = 'ORDER BY PLS_JOBSITE.ACTIVITYDATE ';
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
