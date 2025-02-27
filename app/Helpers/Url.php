<?php

namespace Helpers;

class Url
{
    /**
     * @param string $querystring
     * @param bool $decrypt
     *
     * @return array|null
     */
    public static function parseQueryString(string $querystring = '', bool $decrypt = false): ?array
    {
        $params = [];
        if (empty($querystring)) {
            $querystring = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        } else {
            if ($decrypt) {
                $querystring = urlDecrypt($querystring);
            }
        }

        parse_str($querystring, $params);

        return $params;
    }

    /**
     * @param string $route
     */
    public static function redirect(string $route = ''): void
    {
        $from_url = ($_SERVER['REDIRECT_URL'] ?? null);
        Session::set('return_url', $from_url);

        header('Location: ' . $route);
        exit();
    }

    /**
     * @param string $url
     * @param array $params
     */
    public static function return(string $url = '/', array $params = []): void
    {
        $return_url = !empty(Session::pull('return_url')) ?: $url;

        $args = '';
        if (!empty($params) && is_array($params)) {
            $params = implode('&', $params);

            $append = '?';
            if (strpos($return_url, '?') > 0) {
                $append = '&';
            }

            $args .= $append . $params;
        }

        header('Location: ' . $return_url . $args);
        exit();
    }

    /**
     * Go to previous url
     */
    public static function previous(): void
    {
        header('Location: ' . $_SERVER['HTTP_REFERRER']);
        exit();
    }
}
