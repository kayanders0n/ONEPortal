<?php

namespace Models\EP;

use Core\Model;
use Helpers\Database;

class DBConnectionData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectDBConnectionData(array $filters = [], string $limit = ''): array
    {
        $item_id = (int)$filters['item_id'] ?: null;
        $connected_on = $filters['connected_on'] ?: null;
        $count_only = (int)$filters['count_only'] == 1 ?: false;


        $param_sql = [
            ':ATTACHMENT_ID' => $item_id,
        ];

        $select_sql = 'SELECT
                       MON$ATTACHMENTS.MON$ATTACHMENT_ID AS ATTACHMENT_ID,
                       MON$ATTACHMENTS.MON$REMOTE_ADDRESS AS REMOTE_ADDRESS,
                       MON$ATTACHMENTS.MON$TIMESTAMP AS ATTACHMENT_TIMESTAMP,
                       MON$ATTACHMENTS.MON$USER AS USER_NAME,
                       MON$ATTACHMENTS.MON$REMOTE_PROCESS AS REMOTE_PROCESS,
                       EMPLOYEE.SEQNO AS EMPLOYEE_SEQNO,
                       EMPLOYEE.TABLEID AS EMPLOYEE_TABLEID,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEE.EMAIL AS EMPLOYEE_EMAIL ';

        $from_sql = 'FROM MON$ATTACHMENTS
                     LEFT OUTER JOIN EMPLOYEE
                     ON (MON$ATTACHMENTS.MON$USER = EMPLOYEE.LOGINNAME) ';
        $where_sql = 'WHERE 1 = 1 ';

        if (!$item_id) {
            $where_sql .= 'AND MON$ATTACHMENTS.MON$USER NOT IN (\'SYSDBA\',\'Cache Writer\',\'Garbage Collector\') ';
            $where_sql .= $connected_on ? 'AND MON$ATTACHMENTS.MON$TIMESTAMP ' . $connected_on . ' ' : '';
        } else {
            $where_sql .= $item_id ? 'AND MON$ATTACHMENTS.MON$ATTACHMENT_ID = :ATTACHMENT_ID ' : '';
        }

        $order_sql = 'ORDER BY MON$ATTACHMENTS.MON$ATTACHMENT_ID ';
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
            $query = $admin_db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);
        } else {
            $query = array();
        }
        $count = $admin_db->selectOne('SELECT COUNT(*) as "num_results" ' . $from_sql . $where_sql, $param_sql);

        return [$query, $count->num_results];
    }
}
