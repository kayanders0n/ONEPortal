<?php

namespace Core;

class Path
{
    /**
     * Get uri path segments separated by /
     *
     * @return array
     */
    public function segments(): array
    {
        $uri = explode('?', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        return array_filter(explode('/', $uri[0]));
    }
}
