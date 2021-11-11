<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Page\PageReader;
use App\Template\FrontendTwigRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    protected Request $request;
    protected Response $response;
    protected FrontendTwigRenderer $renderer;
    protected PageReader $pageReader;

    public function __construct(
        Request $request,
        Response $response,
        FrontendTwigRenderer $renderer,
        PageReader $pageReader
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->renderer = $renderer;
        $this->pageReader = $pageReader;
    }
}
