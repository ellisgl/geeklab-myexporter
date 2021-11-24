<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends BaseController
{
    /**
     * Create an HTTP 404 NOT FOUND error page.
     * @return Response
     */
    public function error404(): Response
    {
        $this->response->setStatusCode(404);
        $this->response->setContent('404 - Page not found');

        return $this->response;
    }

    /**
     * Create an HTTP 405 Method NOT ALLOWED error page.
     * @return Response
     */
    public function error405(): Response
    {
        $this->response->setStatusCode(405);
        $this->response->setContent('405 - Method not allowed');

        return $this->response;
    }
}
