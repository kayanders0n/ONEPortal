<?php

namespace Core;

class View
{
    private static $headers = [];

    /**
     * @param string $path
     * @param array $data
     * @param string $error
     */
    public static function render(string $path, array $data, string $error = '')
    {
        self::sendHeaders();

        require APP_PATH . '/views/' . $path . '.php';
    }

    /**
     * @param string $path
     * @param array $data
     */
    public static function renderTemplate(string $path, array $data)
    {
        self::render('header', $data);
        self::render($path, $data);
        self::render('footer', $data);
    }

    /**
     * @param $header
     */
    public static function addHeader($header)
    {
        self::$headers[] = $header;
    }

    /**
     * @param array $headers
     */
    public static function addHeaders(array $headers = [])
    {
        self::$headers = array_merge(self::$headers, $headers);
    }

    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
                header($header);
            }
        }
    }
}
