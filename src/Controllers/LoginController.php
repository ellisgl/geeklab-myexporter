<?php

declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{
    public function index(): void
    {
        $data = [];

        if (Request::METHOD_POST === $this->request->getMethod()) {

        }

        $html = $this->renderer->render('Login', $data);
        $this->response->setContent($html);
    }

}
