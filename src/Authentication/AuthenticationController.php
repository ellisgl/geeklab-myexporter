<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Core\BaseController;
use Exception;
use PDO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthenticationController extends BaseController
{
    /**
     * Perform login.
     *
     * @Todo: Move logic to service or something.
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(): Response
    {
        $data = [];

        if (Request::METHOD_POST === $this->request->getMethod()) {
            try {
                $pdo = new PDO(
                    'mysql:host=' . $this->config->get('servers')[(int)$this->request->request->get('host')]['host'] . ';',
                    $this->request->request->get('username'),
                    $this->request->request->get('password'),
                    [PDO::ATTR_PERSISTENT => false]
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->session->set('loggedIn', 1);
                $this->session->set('dbh', $this->config->get('servers')[(int)$this->request->request->get('host')]['host']);
                $this->session->set('dbu', $this->request->request->get('username'));
                $this->session->set('dbp', $this->request->request->get('password'));
            } catch(Exception $e) {
                $this->session->getFlashBag()->add('error', $e->getMessage());
            }

            foreach($this->session->getFlashBag()->get('warning',[]) as $message) {
                $data['warnings'][] = $message;
            }

            foreach($this->session->getFlashBag()->get('error',[]) as $message) {
                $data['errors'][] = $message;
            }
        }

        if (1 === $this->session->get('loggedIn')) {
            // Go to the main page.
            (new RedirectResponse('/db'))->send();
            exit();
        }

        $data['servers'] = $this->config->get('servers');
        $html = $this->renderer->render('Login', $data);
        $this->response->setContent($html);

        return $this->response;
    }

    /**
     * Log the current user out.
     *
     * @return Response
     */
    public function logout(): Response
    {
        $this->session->clear();

        return new RedirectResponse('/');
    }
}
