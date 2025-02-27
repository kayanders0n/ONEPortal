<?php

namespace Models\EP;

use Core\Model;
use Helpers\Database;

class DBReplicationData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectDBReplicationData(array $filters = [], string $limit = ''): array
    {
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $select_sql = 'SELECT
                       R$LOG.ID AS LOG_ID,
					   R$LOG.TABLE_NAME,
					   CHAR_LENGTH(R$LOG.SQL) AS SQL_LENGTH,
					   R$LOG.IP AS IP_ADDRESS,
					   R$LOG.CREATEDON,
					   R$LOG.CREATEDBYNAME ';

        $from_sql = 'FROM R$LOG ';
        $where_sql = 'WHERE 1 = 1 ';

        $order_sql = 'ORDER BY R$LOG.ID ';
        $limit_sql = $limit ?: '';

        // special Admin DB Connection
        $group = [
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_ADMIN_USER,
            'pass' => DB_ADMIN_PASS
        ];

        $admin_db = Database::get($group);

        if (!$count_only) {
            $query = $admin_db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql);
        } else {
            $query = array();
        }
        $count = $admin_db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql);

        return [$query, $count->num_results];
    }


    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectDBReplicationErrorData(array $filters = [], string $limit = ''): array
    {
        $count_only = (int)$filters['count_only'] == 1 ?: false;

        $select_sql = 'SELECT
                       R$ERROR_LOG.ID AS ERROR_LOG_ID,
					   R$ERROR_LOG.PSQL_MODULE,
					   R$ERROR_LOG.CREATEDON ';

        $from_sql = 'FROM R$ERROR_LOG ';
        $where_sql = 'WHERE 1 = 1 ';

        $order_sql = 'ORDER BY R$ERROR_LOG.ID ';
        $limit_sql = $limit ?: '';

        // special Admin DB Connection
        $group = [
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_ADMIN_USER,
            'pass' => DB_ADMIN_PASS
        ];

        $admin_db = Database::get($group);

        if (!$count_only) {
            $query = $admin_db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql);
        } else {
            $query = array();
        }
        $count = $admin_db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql);

        return [$query, $count->num_results];
    }


}
