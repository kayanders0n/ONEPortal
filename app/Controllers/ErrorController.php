<?php

namespace Controllers;

use Core\Controller;
use Core\View;
use Helpers\Response;

class ErrorController extends Controller
{
    protected string $error;

    /**
     * ErrorController constructor.
     *
     * @param $error
     */
    public function __construct($error)
    {
        $this->error = $error;

        parent::__construct();
    }

    /**
     * @param bool $has_view
     */
    public function view(bool $has_view = false)
    {
        Response::addStatus(404);
        Response::sendHeaders();

        if (!$has_view) {
            $payload = ['message' => Response::$status[404]];
            Response::json($payload);
        }

        $data = $this->registry();

        $data['error'] = $this->error;

        View::render('header', $data);
        View::render('/error/404', $data);
        View::render('footer', $data);
    }

    /**
     * Handle route errors
     */
    public function route()
    {
        $payload = ['message' => Response::$status[404]];

        Response::addStatus(404);
        Response::json($payload);
    }
}
