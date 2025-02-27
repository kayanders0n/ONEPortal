<?php

namespace Controllers\EP;

use Core\Controller;
use Core\Logger;
use Core\View;
use Exception;
use Helpers\Request;
use Helpers\Response;
use Traits\EP\MaterialTrait;

class CatalogController extends Controller
{

    use MaterialTrait;

    public function index()
    {
        $data = $this->registry();

        try {
            // Do the thing
        } catch (Exception $e) {
            $data['message'] = 'No results found';
        }

        // Page Object
        $data['page'] = [
            'slug'        => 'catalog',
            'title'       => 'Catalog',
            'description' => 'Catalog',
            'author'      => ''
        ];

        View::renderTemplate('ep/catalog/index', $data);
    }

}
