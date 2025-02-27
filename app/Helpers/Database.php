<?php

namespace Helpers;

use PDO;
use PDOException;
use PDOStatement;

class Database extends PDO
{
    protected static $instances = [];

    /**
     * @param array $group
     *
     * @return null|Database|mixed
     */
    public static function get(array $group = [])
    {
        $group = !$group ? [
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS
        ] : $group;

        $type = $group['type'];
        $host = $group['host'];
        $name = $group['name'];
        $user = $group['user'];
        $pass = $group['pass'];

        $id = "$type.$host.$name.$user.$pass";

        // Checking if the same
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        try {
            // MAN 07/22/2020
            // ;charset=utf8 do not define the charset, let the driver handle it
            // if you specify this you may get malformed string errors with some values
            $instance = new self("$type:host=$host;dbname=$name", $user, $pass);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Setting Database into $instances to avoid duplication
            self::$instances[$id] = $instance;

            return $instance;

        } catch (PDOException $e) {
            logger(__METHOD__)->exception('PDO Exception', $e);

            return null;
        }
    }

    /**
     * @param $sql
     *
     * @return PDOStatement
     */
    public function raw($sql): PDOStatement
    {
        return $this->query($sql);
    }

    /**
     * @param string $sql
     * @param array $array
     * @param int $fetch_mode
     * @param string $class
     *
     * @return array
     */
    public function select(string $sql, array $array = [], $fetch_mode = PDO::FETCH_OBJ, string $class = ''): array
    {
        $stmt = $this->prepare($sql);
        foreach ($array as $key => $value) {
            if (strpos($sql, $key) == true) {
                if (is_int($value)) {
                    $stmt->bindValue((string) $key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue((string) $key, $value);
                }
            }
        }

        $stmt->execute();

        if ($fetch_mode === PDO::FETCH_CLASS) {
            return $stmt->fetchAll($fetch_mode, $class);
        }

        return $stmt->fetchAll($fetch_mode);
    }

    /**
     * @param $sql
     * @param array $arr
     * @param int $fetch_mode
     * @param string $class
     *
     * @return mixed|null
     */
    public function selectOne($sql, array $arr = [], $fetch_mode = PDO::FETCH_OBJ, $class = '')
    {
        $results = $this->select($sql, $arr, $fetch_mode, $class);

        if (isset($results[0])) {
            return $results[0];
        }

        return null;
    }

    /**
     * @param string $table
     * @param array $data
     * @param null|string $returning
     *
     * @return string
     */
    public function insert(string $table, array $data, string $returning = null): string
    {
        ksort($data);

        $field_names  = implode(',', array_keys($data));
        $field_values = ':' . implode(', :', array_keys($data));

        $stmt = $this->prepare("INSERT INTO $table ($field_names) VALUES ($field_values)".(!empty($returning)?' RETURNING '.$returning:''));

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        //return $this->lastInsertId(); // not supported by firebird PDO

        if (!empty($returning)) {
            $result = $stmt->fetchAll();
            return $result[0][$returning];
        } else {
            return $stmt->rowCount();
        }



    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     *
     * @return int
     */
    public function update(string $table, array $data, array $where): int
    {
        ksort($data);

        $field_details = null;
        foreach ($data as $key => $value) {
            $field_details .= "$key = :field_$key,";
        }
        $field_details = rtrim($field_details, ',');

        $where_details = null;

        $i = 0;
        foreach ($where as $key => $value) {
            if ($i === 0) {
                $where_details .= "$key = :where_$key";
            } else {
                $where_details .= " AND $key = :where_$key";
            }
            $i++;
        }
        $where_details = ltrim($where_details, ' AND ');

        $stmt = $this->prepare("UPDATE $table SET $field_details WHERE $where_details");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * @param string $table
     * @param array $where
     * @param int $limit
     *
     * @return int
     */
    public function delete(string $table, array $where, $limit = 1): int
    {
        ksort($where);

        $where_details = null;

        $i = 0;
        foreach ($where as $key => $value) {
            if ($i === 0) {
                $where_details .= "$key = :$key";
            } else {
                $where_details .= " AND $key = :$key";
            }
            $i++;
        }
//        $where_details = ltrim(" WHERE $where_details", ' AND ');
        $where_details = ' WHERE ' . $where_details;

        //if limit is a number use a limit on the query
        $use_limit = '';
        if (is_numeric($limit)) {
            $use_limit = " ROWS 1 to $limit";
        }

        $stmt = $this->prepare('DELETE FROM ' . $table . $where_details . $use_limit);

        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * @param $table
     *
     * @return int
     */
    public function truncate($table): int
    {
        return $this->exec('TRUNCATE TABLE ' . $table);
    }
}
