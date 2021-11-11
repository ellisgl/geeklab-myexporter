<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Page\PageReader;
use App\Template\FrontendTwigRenderer;
use GeekLab\Conf\GLConf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController
{
    protected GLConf $config;
    protected FrontendTwigRenderer $renderer;
    protected Request $request;
    protected Response $response;
    protected Session $session;

    public function __construct(
        GLConf $config,
        FrontendTwigRenderer $renderer,
        Request $request,
        Response $response,
        Session $session
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->renderer = $renderer;
    }
}
