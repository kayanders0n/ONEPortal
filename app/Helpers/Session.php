<?php

namespace Helpers;

class Session
{
    private static $session_started = false;

    /**
     * If session not started, start it
     */
    public static function init(): void
    {
        if (self::$session_started === false) {
            session_start();
            self::$session_started = true;
        }
    }

    /**
     * @param $key
     * @param bool $value
     */
    public static function set($key, $value = false): void
    {
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $v) {
                $_SESSION[SESSION_PREFIX . $name] = $v;
            }
        } else {
            $_SESSION[SESSION_PREFIX . $key] = $value;
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function exists(string $key): bool
    {
        if (!empty($_SESSION)) {
            $session_key = $_SESSION[SESSION_PREFIX . $key];

            if (!empty($session_key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @param bool $second_key
     *
     * @return null|mixed
     */
    public static function get(string $key, bool $second_key = false)
    {
        if (!empty($_SESSION)) {
            $session_key = $_SESSION[SESSION_PREFIX . $key];

            if (empty($session_key)) {
                return null;
            }

            if ($second_key === true && isset($session_key[$second_key])) {
                return $session_key[$second_key];
            }

            return $session_key;
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public static function pull(string $key)
    {
        if (!empty($_SESSION)) {

            $session_key = $_SESSION[SESSION_PREFIX . $key] ?? null;

            if (empty($session_key)) {
                return null;
            }

            $value = $session_key;

            unset($session_key);
            unset($_SESSION[SESSION_PREFIX . $key]);

            return $value;
        }

        return null;
    }

    /**
     * @return string
     */
    public static function id(): string
    {
        return session_id();
    }

    /**
     * @return string
     */
    public static function regenerate(): string
    {
        session_regenerate_id(true);

        return session_id();
    }

    /**
     * @return mixed
     */
    public static function display()
    {
        return $_SESSION;
    }

    /**
     * @param string $key
     * @param bool $prefix
     */
    public static function destroy($key = '', $prefix = false): void
    {
        if (self::$session_started === true) {

            if ($key === '' && $prefix === false) {
                session_unset();
                session_destroy();
            } else if ($prefix === true) {
                foreach ($_SESSION as $k => $value) {
                    if (strpos($k, SESSION_PREFIX) === 0) {
                        unset($_SESSION[$k]);
                    }
                }
            } else {
                unset($_SESSION[SESSION_PREFIX . $key]);
            }
        }
    }
}
