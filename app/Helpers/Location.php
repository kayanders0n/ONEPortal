<?php

namespace Helpers;

class Location
{
    public static function getLocationByIPAddress() {

        $ip = trim(@$_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip == '') { $ip = $_SERVER["REMOTE_HOST"]; }

        $trim_ip = implode(".", array_slice(explode(".", $ip), 0, 3));

        switch ($trim_ip) {
            case '10.10.0':
            case '10.20.0':
            case '10.21.0':
            case '10.0.0': $result = 'MESA'; break;
            case '172.16.1':
            case '172.16.2':
            case '172.16.0': $result = 'TUCSON'; break;
            case '172.16.21':
            case '172.16.22':
            case '172.16.20': $result = 'WEST'; break;
            case '172.16.31':
            case '172.16.32':
            case '172.16.30': $result = 'EAST'; break;
            default: $result = 'UNKNOWN';
        }

        return $result;
    }
}

