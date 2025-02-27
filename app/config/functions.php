<?php

use Core\Logger;
use Helpers\Arr;

/**
 * @param int $length
 *
 * @return string
 */
function generateToken($length = 32): string
{
    $token = '';

    // Create random token string
    $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    for ($i = 0; $i < $length; $i++) {
        $token .= $string[random_int(0, strlen($string) - 1)];
    }

    return $token;
}

/**
 * @param $input
 *
 * @return string
 */
function base64UrlEncode($input): string
{
    return strtr(base64_encode($input), '+/=', '-_,');
}

/**
 * @param $input
 *
 * @return false|string
 */
function base64UrlDecode($input)
{
    return base64_decode(strtr($input, '-_,', '+/='));
}

/**
 * @param $input
 *
 * @return string
 */
function urlEncrypt($input): string
{
    return base64UrlEncode(convert_uuencode($input));
}

/**
 * @param $input
 *
 * @return string
 */
function urlDecrypt($input): string
{
    return convert_uudecode(base64UrlDecode($input));
}

/**
 * @param $char
 * @param $string
 *
 * @return string
 */
function camelizeString($char, $string): string
{
    return lcfirst(implode('', array_map('ucfirst', array_map('strtolower', explode($char, $string)))));
}

/**
 * @param $char
 * @param $camelized
 *
 * @return string
 */
function unCamelizeString($char, $camelized): string
{
    return implode($char, array_map('strtolower', preg_split('/([A-Z]{1}[^A-Z]*)/', $camelized, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)));
}

/**
 * @param $anstring
 *
 * @return mixed
 */
function alphaNum($anstring)
{
    return preg_replace('/[^A-Za-z0-9\s]/', '', $anstring);
}

/**
 * @param $string
 *
 * @return string
 */
function titleCase($string)
{
    $word_splitters       = [' ', '-', "O'", "L'", "D'", 'St.', 'Mc'];
    $lowercase_exceptions = ['the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'"];
    $uppercase_exceptions = ['III', 'IV', 'VI', 'VII', 'VIII', 'IX', 'AAA', 'AAAA', 'USA'];

    $string = strtolower($string);
    foreach ($word_splitters as $delimiter) {

        $words     = explode($delimiter, $string);
        $new_words = [];

        foreach ($words as $word) {
            if (in_array(strtoupper($word), $uppercase_exceptions)) {
                $word = strtoupper($word);
            } else if (!in_array($word, $lowercase_exceptions)) {
                $word = ucfirst($word);
            }

            $first_two = substr($word, 0, 2);

            if ($first_two == 'S ') {
                $word = lcfirst($word);
            }

            $new_words[] = $word;
        }

        if (in_array(strtolower($delimiter), $lowercase_exceptions)) {
            $delimiter = strtolower($delimiter);
        }

        $string = join($delimiter, $new_words);
    }

    return $string;
}

/**
 * @param string $key
 * @param string $default
 * @param array $options
 *
 * @return bool|null|string|array
 */
function requestParam($key, $default = null, $options = null)
{
    $value = $default;

    if (isset($_POST[$key])) {
        $value = $_POST[$key];
    }
    if (isset($_GET[$key])) {
        $value = $_GET[$key];
    }

    if (is_array($value) && isset($options['implode'])) {
        return implode($options['implode'], $value);
    }

    if (is_array($value) && isset($options['explode'])) {
        return explode($options['explode'], $value);
    }

    if (isset($options['eq'])) {
        return $options['eq'] === $value;
    }

    return $value;
}

/**
 * @param $array
 * @param $key
 * @param null $default
 *
 * @return mixed
 */
function arrayGet($array, $key, $default = null)
{
    return Arr::get($array, $key, $default);
}

/**
 * method name arrayGetEmpty
 *
 * @description make any empty values the default value if the key does not exists or if it is empty
 *
 * @param array $arr
 * @param mixed $key
 *
 * @param null|mixed $default
 *
 * @return mixed|null
 */
function arrayGetEmpty(array $arr, $key, $default = null)
{
    //get the value
    $value = arrayGet($arr, $key);

    //make sure there are no spaces or tab chars
    $value = trim($value);

    //return the original value or the default value
    return !empty($value) ? arrayGet($arr, $key) : $default;
}

/**
 * @param $key_array
 * @param $value_array
 *
 * @return mixed
 */
function arrayFillKeysValues($key_array, $value_array)
{
    if (is_array($key_array)) {
        foreach ($key_array as $key => $value) {
            $filled_array[$value] = $value_array[$key];
        }
    }

    return $filled_array;
}

/**
 * @param $key_array
 * @param $value_array
 *
 * @return mixed
 */
function arrayScopeKeys($key_array, $value_array)
{
    if (is_array($key_array)) {
        foreach ($key_array as $key) {
            $selected_keys[$key] = $value_array[$key];
        }
    }

    return $selected_keys;
}

/**
 * @param $array
 * @param $key
 * @param $value
 *
 * @return array
 */
function arrayKeyValueSearch($array, $key, $value)
{
    $results = [];
    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
        foreach ($array as $subArray) {
            $results = array_merge($results, arrayKeyValueSearch($subArray, $key, $value));
        }
    }

    return $results;
}

/**
 * @param $name
 * @param $type
 * @param $tmp_name
 * @param $error
 * @param $size
 *
 * @return array
 */
function remapFilesArray($name, $type, $tmp_name, $error, $size): array
{
    return [
        'name'     => $name,
        'type'     => $type,
        'tmp_name' => $tmp_name,
        'error'    => $error,
        'size'     => $size,
    ];
}

if (!function_exists('xssEncode')) {
    /**
     * @param $value
     * @param bool $quotes
     *
     * @return mixed
     */
    function xssEncode($value, $quotes = false)
    {
        return \Helpers\XSS::encode($value, $quotes);
    }
}

/**
 * @param $script_path
 *
 * @return string|null
 */
function includePageScript($script_path, $script_name, $is_module=false)
{
    if (file_exists(VIEWS . '/' . config('app.portal') . '/' . $script_path . '/js/' . $script_name)) {
        return '<script src="/app/views/' . config('app.portal') . '/' . $script_path . '/js/' . $script_name . '?r=' . rand() . '"' . ($is_module?' type="module"':'') . '></script>';
    }

    return null;
}


function cleanString($string, $superClean = false)
{

    $result = trim($string); // normal trim
    $result = trim($result, chr(0xC2) . chr(0xA0)); // weird non breaking spaces
    $result = str_replace(['®', '™', '©'], ['', '', ''], utf8_encode($result)); // strip out TM and R marks
    if ($superClean) { // super clean, strip anything outside 32-126 ascii
        $result = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $result);
    }

    return $result;
}

function javaSafe($name)
{
    $return = strtolower($name);
    $return = str_replace('-', '_', $return);
    $return = str_replace(' ', '-', $return);
    $return = str_replace('#', '_', $return);
    $return = str_replace(':', '_', $return);
    $return = str_replace('/', '_', $return);
    $return = str_replace('.', '_', $return);
    $return = str_replace("'", '`', $return);

    return $return;
}

function sanitizeFilename($string) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
        "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
        "—", "–", ",", "<", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    return $clean;
}

if (!function_exists('logger')) {

    /**
     * @param null|string $method
     * @param null|string $level
     * @param null|string $message
     * @param null|array $context
     *
     * @return Logger
     */
    function logger(string $method = null, string $level = null, string $message = null, array $context = null): Logger
    {
        if (empty($message)) {
            return new Logger($method);
        }

        if (empty($level)) {
            $level = 'debug';
        }

        return (new Logger($method))->$level($message, $context);
    }
}
