<?php

namespace Models;

use Core\Model;

class Users extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectUsers(array $filters = [], string $limit = ''): array
    {
        $param_sql = [
            ':USER_ID'   => (int) ($filters['user_id'] ?? null),
            ':USER_NAME' => strtoupper($filters['user_name'] ?? null),
            ':PASSWORD'  => (string) ($filters['password'] ?? null),
            ':PASSCODE'  => (string) ($filters['passcode'] ?? null),
            ':COMPLETED' => (int) ($filters['completed'] ?? null)
        ];

        $select_sql = 'SELECT  
                       EMPLOYEE.SEQNO,
                       EMPLOYEE.TABLEID as EMPLOYEE_NUM,
                       EMPLOYEE.DESCRIPT AS EMPLOYEE_NAME,
                       EMPLOYEE.FIRSTNAME AS EMPLOYEE_FIRSTNAME,
                       EMPLOYEE.LASTNAME AS EMPLOYEE_LASTNAME,
                       EMPLOYEE.ZIP, EMPLOYEE.WORKPHONE,
                       EMPLOYEE.LOGINNAME AS EMPLOYEE_LOGINNAME,
                       EMPLOYEE.EMAIL AS EMPLOYEE_EMAIL,
                       EMPLOYEE.PW AS EMPLOYEE_PW,
                       EMPLOYEE.PW_HASH AS EMPLOYEE_PW_HASH,
                       EMPLOYEE.PW_CODE AS EMPLOYEE_PW_CODE,
                       EMPLOYEE.PW_CODE_DATE AS EMPLOYEE_PW_CODE_DATE,
                       EMPLOYEEPAYROLL.SOURCEENTITYID AS EMPLOYEE_SOURCEENTITYID,
                       EMPLOYEEPAYROLL.SALARYGLPREFIX AS EMPLOYEE_GLPREFIX,
                       EMPLOYEEPAYROLL.SALARYGLSUFFIX AS EMPLOYEE_GLSUFFIX,
                       EMPLOYEE.TYPEID AS EMPLOYEE_TYPEID ';
        $from_sql   = 'FROM EMPLOYEE 
                       INNER JOIN EMPLOYEEPAYROLL ON (EMPLOYEE.SEQNO = EMPLOYEEPAYROLL.SEQNO) ';
        $where_sql  = 'WHERE 1 = 1 ';
        $where_sql  .= !empty($filters['user_id']) ? 'AND (EMPLOYEE.TABLEID = :USER_ID) ' : '';
        $where_sql  .= !empty($filters['user_name']) ? 'AND (EMPLOYEE.LOGINNAME = :USER_NAME) ' : '';
        $where_sql  .= !empty($filters['password']) ? 'AND (EMPLOYEE.PW_HASH = :PASSWORD) ' : '';
        $where_sql  .= !empty($filters['passcode']) ? 'AND (EMPLOYEE.PW_CODE = :PASSCODE) ' : '';
        $where_sql  .= isset($filters['completed']) ? 'AND (EMPLOYEE.COMPLETED = :COMPLETED) ' : '';
        $order_sql  = '';
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
    public function insertUser(array $request): ?int
    {
        if (!empty($request)) {

            $password_hash = password_hash(md5(trim($request['password'])), PASSWORD_BCRYPT, ['cost' => 12]);

            $inserted = $this->db->insert('EMPLOYEE', [
                'SEQNO'     => '???', // TODO: do we need this on insertion?
                'TABLEID'   => $request['user_id'], // TODO: we don't have this yet
                'LOGINNAME' => $request['user_name'],
                'DESCRIPT'  => $request['description'],
                'EMAIL'     => $request['email'],
                'PW_HASH'   => $password_hash,
                'TYPEID'    => $request['type_id']
            ]);

            if (!empty($inserted)) {

                $this->db->insert('EMPLOYEEPAYROLL', [
                    'SEQNO'          => '???', // TODO: do we need this on insertion?
                    'SOURCEENTITYID' => $request['company_id'],
                    'SALARYGLPREFIX' => '???',
                    'SALARYGLSUFFIX' => '???'  // user_role
                ]);

                return $inserted;
            }
        }

        return null;
    }

    /**
     * @param array $request
     *
     * @return null|int
     */
    public function updateUserPassword(array $request): ?int
    {
        if (!empty($request['user_id']) && !empty($request['password'])) {

            $password_hash = password_hash(md5(trim($request['password'])), PASSWORD_BCRYPT, ['cost' => 12]);

            return $this->db->update('EMPLOYEE', [
                'PW_HASH'      => $password_hash,
                'PW_CODE'      => null,
                'PW_CODE_DATE' => null
            ], ['TABLEID' => $request['user_id']]);
        }

        return null;
    }

    /**
     * @param array $request
     *
     * @return null|int
     */
    public function updateUserPasscode(array $request): ?int
    {
        if (!empty($request['user_id']) && !empty($request['passcode'])) {

            return $this->db->update('EMPLOYEE', [
                'PW_CODE'      => $request['passcode'],
                'PW_CODE_DATE' => date('Y-m-d H:i:s')
            ], ['TABLEID' => $request['user_id']]);
        }

        return null;
    }

    /**
     * @param array $request
     *
     * @return null|int
     */
    public function deleteUserPasscode(array $request): ?int
    {
        return $this->db->update('EMPLOYEE', [
            'PW_CODE'      => null,
            'PW_CODE_DATE' => null
        ], ['TABLEID' => $request['user_id']]);
    }
}
