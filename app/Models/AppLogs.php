<?php

namespace Models;

use Core\Model;

class AppLogs extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return null|array
     */
    public function selectAppLogs(array $filters = [], string $limit = ''): ?array
    {
        $param_sql = [
            ':GROUP_NAME'  => $filters['group_name'],
            ':STREAM_NAME' => $filters['stream_name'],
            ':LEVEL_NAME'  => $filters['level_name'],
            ':PUSHED'      => $filters['pushed']
        ];

        $select_sql = 'SELECT 
                       WEB_APPLOG.ID
                       WEB_APPLOG.GROUP_NAME
                       WEB_APPLOG.STREAM_NAME
                       WEB_APPLOG.LEVEL_NAME
                       WEB_APPLOG.METHOD
                       WEB_APPLOG.MESSAGE
                       WEB_APPLOG.CONTEXT
                       WEB_APPLOG.PUSHED
                       WEB_APPLOG.CREATEDON ';
        $from_sql   = 'FROM WEB_APPLOG ';
        $where_sql  = 'WHERE 1 ';
        $where_sql  .= $filters['group_name'] ? 'AND GROUP_NAME = :GROUP_NAME ' : '';
        $where_sql  .= $filters['stream_name'] ? 'AND STREAM_NAME = :STREAM_NAME ' : '';
        $where_sql  .= $filters['level_name'] ? 'AND LEVEL_NAME = :LEVEL_NAME ' : '';
        $where_sql  .= $filters['pushed'] ? 'AND PUSHED = :pushed ' : '';
        $order_sql  = 'ORDER BY CREATEDON DESC ';
        $limit_sql  = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        $count = $this->db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }

    /**
     * @param array $request
     *
     * @return null|int
     */
    public function insertAppLog(array $request): ?int
    {
        if (!empty($request)) {

            return $this->db->insert('WEB_APPLOG', [
                'GROUP_NAME'  => $request['group_name'] ?: null,
                'STREAM_NAME' => $request['stream_name'] ?: null,
                'LEVEL_NAME'  => $request['level_name'] ?: null,
                'METHOD'      => $request['method'] ?: null,
                'MESSAGE'     => $request['message'] ?: null,
                'CONTEXT'     => $request['context'] ?: null
            ]);
        }

        return null;
    }
}
