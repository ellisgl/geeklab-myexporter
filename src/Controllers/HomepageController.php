<?php

declare(strict_types=1);

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageController
{
    /** @var Request $request */
    private $request;
    /** @var Response $response */
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

    }

    public function index(): void
    {
        $this->response->setContent('Controller Injected "Hello, World!"');
    }

    public function test(): void
    {
        $this->response->setContent('XYZ');
    }
}
