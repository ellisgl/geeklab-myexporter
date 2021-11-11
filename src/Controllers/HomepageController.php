<?php

declare(strict_types=1);

namespace App\Controllers;

class HomepageController extends BaseController
{
    public function index(): void
    {
        $this->response->setContent('Controller Injected "Hello, World!"');
    }

    public function test(): void
    {
        $this->response->setContent('XYZ');
    }
}
