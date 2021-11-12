<?php

declare(strict_types=1);

namespace App\Controllers;

use \Exception;
use \PDO;
use Symfony\Component\HttpFoundation\Request;

class DbController extends BaseController
{
    public function index(): void
    {
        $this->checkAuthenticated();
        $this->response->setContent('Hello!');
    }

}
