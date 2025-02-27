<?php

namespace Helpers;

class Request
{
    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function post($key)
    {
        if (empty($key)) {
            return $_POST ?? null;
        }

        return $_POST[$key] ?? null;
    }

    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function get($key)
    {
        if (empty($key)) {
            return $_GET ?? null;
        }

        return $_GET[$key] ?? null;
    }

    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function files($key)
    {
        if (empty($key)) {
            return $_FILES ?? null;
        }

        return $_FILES[$key] ?? null;
    }

    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function query($key)
    {
        if (empty($key)) {
            return $_GET ?? null;
        }

        return $_GET[$key] ?? null;
    }

    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function put($key)
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        if (empty($key)) {
            return $_PUT ?? null;
        }

        return $_PUT[$key] ?? null;
    }

    /**
     * @param string $key
     * @static static method
     * @return mixed
     */
    public static function del($key)
    {
        parse_str(file_get_contents('php://input'), $_DEL);

        return $_DEL[$key] ?? null;
    }

    /**
     * returns if request made is ajax or not
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * @static static method
     * @return bool
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @static static method
     * @return bool
     */
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * @static static method
     * @return bool
     */
    public static function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    /**
     * @static static method
     * @return bool
     */
    public static function isDelete(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    /**
     * @return array
     */
    public static function parseStr(): array
    {
        parse_str(file_get_contents("php://input"), $request);

        return $request;
    }

    /**
     * @return null|array
     */
    public static function jsonInput(): ?array
    {
        $input = file_get_contents('php://input');

        if (!empty($input)) {
            return json_decode(file_get_contents('php://input'), true);
        }

        return null;
    }

    /**
     * @param array $array
     * @param bool $filter
     *
     * @return array|null
     */
    public static function filterRequest(array &$array, $filter = false): ?array
    {
        array_walk_recursive($array, function (&$value) use ($filter) {
            $value = trim($value);
            if ($filter) {
                $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
            if (strlen($value) == 0) {
                $value = null;
            }
        });

        if (!empty($array)) {
            return $array;
        }

        return null;
    }

    /**
     * @return null|array
     */
    public static function jsonFilter(): ?array
    {
        $json    = self::jsonInput();
        $request = self::filterRequest($json, true);

        return $request;
    }

    /**
     * Gets the request method.
     * @return string
     */
    public static function getMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } else if (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        return strtoupper($method);
    }

    /**
     * @return mixed
     */
    public static function realIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}
