<?php

namespace Helpers;

use Exception;
use Helpers\Database;

class DatabaseNoteUpdate
{

    /***
     * @param Database $db
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */
    public static function update(Database $db, string $table, array $data, array $where): int
    {

        $field_details = null;
        if ((!empty($data['note_field'])) &&
            (!empty($data['note'])) &&
            (!empty($data['user_name'])))
        {

            if (!empty($data['update'])) {

                foreach ($data['update'] as $key => $value) {
                    $field_details .= "$key = :field_$key,";
                }

            }

            $field_details .= $data['note_field'] . ' = :note_data ';
            $field_details .= '|| ASCII_CHAR(13) || ASCII_CHAR(10) || ASCII_CHAR(13) || ASCII_CHAR(10) || COALESCE(NOTE, \'\')';

        } else {
            throw new Exception('Missing required fields');
        }

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

        $note_data = $data['user_name'] . ' ' . date('m/d/Y h:iA')."\r\n" . html_entity_decode($data['note'], ENT_QUOTES);

        $stmt = $db->prepare("UPDATE $table SET $field_details WHERE $where_details");

        $stmt->bindValue('note_data', $note_data);

        foreach ($data['update'] as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        }

        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }


}
