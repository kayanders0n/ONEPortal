<?php

namespace Core;

/**
 * $actions can be controllers or closures
 *
 * @method static any(string $route, $action)
 * @method static get(string $route, $action)
 * @method static post(string $route, $action)
 * @method static put(string $route, $action)
 * @method static patch(string $route, $action)
 * @method static delete(string $route, $action)
 * @method static match(string $route, $action)
 */
class Router
{
    public static $callbacks = [];
    public static $errorCallback;
    public static $fallback = true;
    public static $halts = true;
    public static $routes = [];
    public static $methods = [];
    public static $patterns = [
        ':all'    => '.*',
        ':alpha'  => '[[:alpha:]]+',
        ':alnum'  => '[[:alnum:]]+',
        ':any'    => '[^/]+',
        ':hex'    => '[[:xdigit:]]+',
        ':num'    => '[[:digit:]]+',
        ':slug'   => '[[:alnum:]-]+',
        ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}'
    ];

    /**
     * @param $method
     * @param $params
     */
    public static function __callStatic($method, $params)
    {
        $uri      = dirname($_SERVER['PHP_SELF']) . '/' . $params[0];
        $callback = $params[1];

        self::$routes[]    = $uri;
        self::$methods[]   = strtoupper($method);
        self::$callbacks[] = $callback;
    }

    /**
     * @param $callback
     */
    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    /**
     * @param bool $flag
     */
    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    /**
     *
     */
    public static function dispatch()
    {
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//', '/', self::$routes);

        $found_route = false;

        if (strpos($uri, '&') > 0) {
            $query = substr($uri, strpos($uri, '&') + 1);
            $uri   = substr($uri, 0, strpos($uri, '&'));
            $q_arr = explode('&', $query);
            foreach ($q_arr as $q) {
                $qobj    = explode('=', $q);
                $q_arr[] = [$qobj[0] => $qobj[1]];
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        // check if route is defined without regex
        if (in_array($uri, self::$routes, true)) {
            $route_pos = array_keys(self::$routes, $uri);

            // foreach route position
            foreach ($route_pos as $route) {
                if (self::$methods[$route] == $method || self::$methods[$route] === 'ANY') {
                    $found_route = true;

                    //if route is not an object
                    if (!is_object(self::$callbacks[$route])) {
                        //call object controller and method
                        self::invokeObject(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    } else {
                        //call closure
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    }
                }
            }

        } else {
            // check if defined with regex
            $pos = 0;

            // foreach routes
            foreach (self::$routes as $route) {
                $route = str_replace('//', '/', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^' . $route . '$#', $uri, $matched)) {

                    if (self::$methods[$pos] === $method || self::$methods[$pos] === 'ANY') {
                        $found_route = true;

                        //remove $matched[0] as [1] is the first parameter.
                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            //call object controller and method
                            self::invokeObject(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        } else {
                            //call closure
                            call_user_func_array(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        }
                    }
                }
                $pos++;
            }
        }

        if (self::$fallback) {
            //call the auto dispatch method
            $found_route = self::autoDispatch();
        }

        // run the error callback if the route was not found
        if (!$found_route) {
            if (!is_object(self::$errorCallback)) {
                //call object controller and method
                self::invokeObject(self::$errorCallback, null, 'No routes found.');
                if (self::$halts) {
                    return;
                }
            } else {
                call_user_func(self::$errorCallback);
                if (self::$halts) {
                    return;
                }
            }
        }
    }

    /**
     * @param $callback
     * @param null $matched
     * @param null $msg
     */
    public static function invokeObject($callback, $matched = null, $msg = null)
    {
        $last = explode('/', $callback);
        $last = end($last);

        [$controller, $method] = explode('@', $last);

        $controller = new $controller($msg);
        call_user_func_array([$controller, $method], $matched ?: []);
    }

    /**
     * @return bool
     */
    public static function autoDispatch(): bool
    {
        $uri = parse_url($_SERVER['QUERY_STRING'], PHP_URL_PATH);
        $uri = '/' . $uri;
        if (strpos($uri, '/') === 0) {
            $uri = substr($uri, strlen('/'));
        }
        $uri = trim($uri, ' /');
        $uri = ($amp = strpos($uri, '&')) !== false ? substr($uri, 0, $amp) : $uri;

        $parts = explode('/', $uri);

        $controller = array_shift($parts);
        $controller = $controller ?: DEFAULT_CONTROLLER;
        $controller = ucwords($controller);

        $method = array_shift($parts);
        $method = $method ?: DEFAULT_METHOD;

        $args = !empty($parts) ? $parts : [];

        if (!is_file(APP_PATH . 'Controllers/' . $controller . '.php')) {
            return false;
        }

        $controller = '\Controllers\\' . $controller;
        $c          = new $controller;

        if (method_exists($c, $method)) {
            call_user_func_array([$c, $method], $args);

            return true;
        }

        return false;
    }
}
