<?php

declare(strict_types=1);

namespace App\Core;

use App\Authentication\NotLoggedInException;
use App\Core\Template\TwigRenderer;
use GeekLab\Conf\GLConf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController
{
    protected GLConf       $config;
    protected TwigRenderer $renderer;
    protected Request      $request;
    protected Response     $response;
    protected Session      $session;

    public function __construct(
        GLConf $config,
        TwigRenderer $renderer,
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

    /**
     * @Todo: Does this need to be moved?
     *
     * @throws NotLoggedInException
     */
    protected function checkAuthenticated(): void
    {
        if (!$this->session->get('loggedIn')) {
            throw new NotLoggedInException('NOT LOGGED IN');
        }
    }
}
